<?php

namespace Application\Controller;

use Zend\File\Transfer\Adapter\Http;

use Zend\Validator\File\Size;


use Zend\ProgressBar\ProgressBar;
use Zend\Loader\StandardAutoloader;
use Zend\Validator\AbstractValidator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Produto;
use Application\Form\ProdutoForm;

class ProdutosController extends AbstractActionController
{
	
	protected $produtoTable;
	
	public function getProdutoTable() {
		if (!$this->produtoTable) {
			$sm = $this->getServiceLocator();
			$this->produtoTable = $sm->get('produto_table');
		}
		
		return $this->produtoTable;
	}
	
	public function IndexAction() {
		$messages = $this->flashMessenger()->getMessages();		
		$pageNumber = (int)$this->params()->fromQuery('pagina');
		
		if ($pageNumber == 0) {
			$pageNumber = 1;
		}
		
		$produtos = $this->getProdutoTable()->fetchAll($pageNumber, 20);			
		
		return new ViewModel(array(
			'messages' => $messages,
			'produtos' => $produtos,
			'titulo'    => 'Listagem de Produtos'	
		));
	}
	
	public function novoAction() {
		$form = new ProdutoForm();
		
		$request = $this->getRequest();
		if ($request->isPost()) {								
			$produto = new Produto();
			
			$form->setInputFilter($produto->getInputFilter());
			$File = $this->params()->fromFiles('produto_foto');
			
			/* $nonFile = $request->getPost()->toArray();						
			$data    = array_merge($nonFile,array('produto_foto' => $File['name'])); */									

			$data = array_merge_recursive(
					$this->getRequest()->getPost()->toArray(),
					$this->getRequest()->getFiles()->toArray()					
			);
			$form->setData($data);
						
			if ($form->isValid()) {		
				$size = new Size(array('max' => 2000000));
				$adapter = new Http();
				$adapter->setValidators(array($size),$File['name']);
				
				if (!$adapter->isValid()) {
					$dataError = $adapter->getMessages();
					$error = array();
					foreach ($dataError as $row) {
						$error[] = $row;
					}
					$form->setMessages(array('produto_foto' => $error));
				} else {
					$diretorio = $request->getServer()->DOCUMENT_ROOT . '/projetos/crud_prod/public/conteudos/produtos';
					$adapter->setDestination($diretorio);
				
					if ($adapter->receive($File['name'])) {
						$this->flashMessenger()->addMessage(array('success' => 'A foto foi enviada com sucesso!'));
					} else {
						$this->flashMessenger()->addMessage(array('error' => 'A foto foi não foi enviada'));
					}
				}
							
				$produto->exchangeArray($form->getData());
				$this->getProdutoTable()->saveProduto($produto);
				
				$this->flashMessenger()->addMessage(array('success' => 'Cadastro efetuado com sucesso!'));
				$this->redirect()->toUrl('http://localhost/projetos/crud_prod/public/produtos');
			}			
		}
		
		$view = new ViewModel(array(
			'form' => $form
		));
		
		$view->setTemplate('application/produtos/form.phtml');
		
		return $view;
	}
	
	public function editarAction() {
		$id = $this->params('id');
		$produto = $this->getProdutoTable()->getProduto($id);		
		
		$form = new ProdutoForm();
				
		$form->bind($produto);
		$form->get('produto_submit')->setLabel('Alterar');
		
		$request = $this->getRequest();		
		if ($request->isPost()) {			
			$form->setInputFilter($produto->getInputFilter());
			$File = $this->params()->fromFiles('produto_foto');						
			
			$data = array_merge_recursive(
					$this->getRequest()->getPost()->toArray(),
					$this->getRequest()->getFiles()->toArray()
			);
			$form->setData($data);																				
			
			if ($form->isValid()) {	
				$form->bind($produto);
				$size = new Size(array('max' => 2000000));
				$adapter = new Http();
				$adapter->setValidators(array($size),$File['name']);
				
				if (!$adapter->isValid()) {					
					$dataError = $adapter->getMessages();
					$error = array();
					foreach ($dataError as $row) {
						$error[] = $row;
					}
					$form->setMessages(array('produto_foto' => $error));
				} else {					
					$diretorio = $request->getServer()->DOCUMENT_ROOT . '/projetos/crud_prod/public/conteudos/produtos';					
					$adapter->setDestination($diretorio);
				
					if ($adapter->receive($File['name'])) {
						$this->flashMessenger()->addMessage(array('success' => 'A foto foi enviada com sucesso!'));						
					} else {
						$this->flashMessenger()->addMessage(array('error' => 'A foto foi não foi enviada'));						
					}
				}				
																			
				$this->getProdutoTable()->saveProduto($produto);
				$this->flashMessenger()->addMessage(array('success' => 'Produto atualizado com sucesso!'));
				$this->redirect()->toUrl('http://localhost/projetos/crud_prod/public/produtos');
			}
		}
		
		$view = new ViewModel(array(
			'form' => $form
		));
		$view->setTemplate('application/produtos/form.phtml');
		
		return $view;
	}
}