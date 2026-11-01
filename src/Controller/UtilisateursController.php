<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

class UtilisateursController extends AppController
{
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Authentication->allowUnauthenticated(['login']);
    }

    public function login()
    {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();

        if ($result !== null && $result->isValid()) {
        $role = $result->getData()->get('role');
        $parDefaut = $role === 'administrateur'
        ? ['controller' => 'Etablissements', 'action' => 'administration']
        : ['controller' => 'Reservations', 'action' => 'gestion'];
        $cible = $this->Authentication->getLoginRedirect() ?? $parDefaut;

            return $this->redirect($cible);
        }
        if ($this->request->is('post')) {
            $this->Flash->error('E-mail ou mot de passe incorrect.');
        }
    }

    public function logout()
    {
        $result = $this->Authentication->getResult();
        if ($result !== null && $result->isValid()) {
            $this->Authentication->logout();
        }

        return $this->redirect(['action' => 'login']);
    }
}