<?php

namespace Application\Form;



use Zend\Form\Element\Button;

use Zend\Form\Element;

use Zend\Form\Element\Checkbox;

use Zend\Form\Element\Textarea;

use Zend\Form\Element\File;

use Zend\Form\Element\Text;

use Zend\Form\Element\Hidden;

use Zend\Form\Form;

use Zend\InputFilter;

class ProdutoForm extends Form
{
	public function __construct($name = null) {
		parent::__construct('produto');	
		
		//$this->setAttribute('enctype','multipart/form-data');
		
		$id = new Hidden('produto_id');		
		$nome = new Text('produto_nome');
		$nome->setLabel('Nome:')
			->setAttributes(array(
				'style' => 'width:500px;'
			));
					
		$preco = new Text('produto_preco');
		$preco->setLabel('Preço:')
			->setAttributes(array(
					'style' => 'width:100px;'
			));
		
		$foto = new File('produto_foto');
		$foto->setLabel('Foto:')
			->setAttributes(array(
					'style' => 'width:500px;'
			));
		
		$descricao = new Textarea('produto_descricao');
		$descricao->setLabel('Descrição:')
			->setAttributes(array(
					'style' => 'width:500px;height:100px;'				
			));

		$status = new Checkbox('produto_status');
		$status->setLabel('Ativo:')
			->setValue(1);
		
		$submit = new Button('produto_submit');
		$submit->setLabel('Cadastrar')
			->setAttributes(array(
					'type'=>'submit',
					'style' => 'margin-top:15px'
			));
		
		//setando os campos criados			
		$this->add($id);
		$this->add($nome);
		$this->add($preco);
		$this->add($foto);
		$this->add($descricao);
		$this->add($status);
		$this->add($submit, array('priority' => -100));
		
		$this->addInputFilter();
	}
	

	public function addInputFilter() {
		$inputFilter = new InputFilter\InputFilter();
				
		// File Input
		$fileInput = new InputFilter\FileInput('produto_foto');
		$fileInput->setRequired(false);
		$fileInput->getFilterChain()->attachByName(
				'filerenameupload',
				array(
						'target'    => './data/tmpuploads/avatar.png',
						'randomize' => true,
				)
		);
		$inputFilter->add($fileInput);
	
		$this->setInputFilter($inputFilter);
	}
}