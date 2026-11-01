<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;

class EtablissementsController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['inscription']);
    }

    /**
     * Inscription publique d'un établissement avec son compte gestionnaire.
     * L'établissement reste invisible jusqu'à sa validation par l'administrateur.
     */
    public function inscription()
    {
        $erreurs = [];
        $donnees = $this->request->getData();

        if ($this->request->is('post')) {
            $nom = trim((string)($donnees['nom'] ?? ''));
            $type = (string)($donnees['type'] ?? '');
            $nomGestionnaire = trim((string)($donnees['nom_gestionnaire'] ?? ''));
            $email = trim((string)($donnees['email'] ?? ''));
            $motDePasse = (string)($donnees['mot_de_passe'] ?? '');

            if ($nom === '') {
                $erreurs[] = "Le nom de l'établissement est obligatoire.";
            }
            if (!in_array($type, ['salle', 'restaurant', 'hotel'], true)) {
                $erreurs[] = "Choisissez le type d'établissement.";
            }
            if ($nomGestionnaire === '') {
                $erreurs[] = 'Votre nom est obligatoire.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs[] = 'Adresse e-mail invalide.';
            }
            if (strlen($motDePasse) < 8) {
                $erreurs[] = 'Le mot de passe doit faire au moins 8 caractères.';
            }
            if ($motDePasse !== (string)($donnees['confirmation'] ?? '')) {
                $erreurs[] = 'Les deux mots de passe ne correspondent pas.';
            }

            if (!$erreurs) {
                $utilisateurs = $this->fetchTable('Utilisateurs');
                $ok = $this->Etablissements->getConnection()->transactional(
                    function () use ($donnees, $nom, $type, $nomGestionnaire, $email, $motDePasse, $utilisateurs) {
                        $etablissement = $this->Etablissements->newEntity([
                            'nom' => $nom,
                            'type' => $type,
                            'adresse' => trim((string)($donnees['adresse'] ?? '')) ?: null,
                            'telephone' => trim((string)($donnees['telephone'] ?? '')) ?: null,
                        ]);
                        $etablissement->valide = false;
                        if (!$this->Etablissements->save($etablissement)) {
                            return false;
                        }

                        $compte = $utilisateurs->newEntity([
                            'nom' => $nomGestionnaire,
                            'email' => $email,
                            'mot_de_passe' => $motDePasse,
                            'role' => 'gestionnaire',
                            'etablissement_id' => $etablissement->id,
                        ]);
                        if (!$utilisateurs->save($compte)) {
                            return false;
                        }

                        return true;
                    }
                );
                if ($ok) {
                    $this->Flash->success('Inscription enregistrée. Vous pouvez vous connecter ; votre établissement sera visible après validation.');

                    return $this->redirect(['controller' => 'Utilisateurs', 'action' => 'login']);
                }
                $erreurs[] = 'Inscription impossible (cette adresse e-mail est peut-être déjà utilisée).';
            }
        }

        unset($donnees['mot_de_passe'], $donnees['confirmation']);
        $this->set(compact('erreurs', 'donnees'));
    }

    public function administration()
    {
        $this->exigerAdministrateur();
        $etablissements = $this->Etablissements->find()
            ->orderBy(['valide' => 'ASC', 'created' => 'DESC'])
            ->all();

        $gestionnaires = [];
        $comptes = $this->fetchTable('Utilisateurs')->find()->where(['role' => 'gestionnaire'])->all();
        foreach ($comptes as $compte) {
            $gestionnaires[(int)$compte->etablissement_id][] = $compte->email;
        }
        $this->set(compact('etablissements', 'gestionnaires'));
    }

    public function valider(?string $id = null)
    {
        $this->request->allowMethod(['post']);
        $this->exigerAdministrateur();
        $etablissement = $this->Etablissements->get((int)$id);
        $etablissement->valide = true;
        $this->Etablissements->saveOrFail($etablissement);
        $this->Flash->success('Établissement validé : il est maintenant visible du public.');

        return $this->redirect(['action' => 'administration']);
    }

    public function suspendre(?string $id = null)
    {
        $this->request->allowMethod(['post']);
        $this->exigerAdministrateur();
        $etablissement = $this->Etablissements->get((int)$id);
        $etablissement->valide = false;
        $this->Etablissements->saveOrFail($etablissement);
        $this->Flash->success("Établissement suspendu : il n'est plus visible du public.");

        return $this->redirect(['action' => 'administration']);
    }

    private function exigerAdministrateur(): void
    {
        if ($this->Authentication->getIdentity()?->get('role') !== 'administrateur') {
            throw new ForbiddenException('Accès réservé aux administrateurs.');
        }
    }
}