<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Ressources Controller
 *
 * @property \App\Model\Table\RessourcesTable $Ressources
 */
class RessourcesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
    $query = $this->Ressources->find()->contain(['Etablissements']);

    $type = $this->request->getQuery('type');
    if (!empty($type)) {
        $query->where(['Ressources.type' => $type]);
    }
    $capacite = (int)$this->request->getQuery('capacite');
    if ($capacite > 0) {
        $query->where(['Ressources.capacite >=' => $capacite]);
    }

    $ressources = $this->paginate($query);
    $this->set(compact('ressources'));
    }

    /**
     * View method
     *
     * @param string|null $id Ressource id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $ressource = $this->Ressources->get($id, contain: ['Etablissements', 'Blocages', 'Reservations']);
        $this->set(compact('ressource'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $ressource = $this->Ressources->newEmptyEntity();
        if ($this->request->is('post')) {
            $ressource = $this->Ressources->patchEntity($ressource, $this->request->getData());
            if ($this->Ressources->save($ressource)) {
                $this->Flash->success(__('The ressource has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The ressource could not be saved. Please, try again.'));
        }
        $etablissements = $this->Ressources->Etablissements->find('list', limit: 200)->all();
        $this->set(compact('ressource', 'etablissements'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Ressource id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $ressource = $this->Ressources->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $ressource = $this->Ressources->patchEntity($ressource, $this->request->getData());
            if ($this->Ressources->save($ressource)) {
                $this->Flash->success(__('The ressource has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The ressource could not be saved. Please, try again.'));
        }
        $etablissements = $this->Ressources->Etablissements->find('list', limit: 200)->all();
        $this->set(compact('ressource', 'etablissements'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Ressource id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $ressource = $this->Ressources->get($id);
        if ($this->Ressources->delete($ressource)) {
            $this->Flash->success(__('The ressource has been deleted.'));
        } else {
            $this->Flash->error(__('The ressource could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
