<?php

namespace application\Model;

use Zend\I18n\Validator\Float;
use Zend\Validator\NotEmpty;
use Zend\Validator\AbstractValidator;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;


class Produto implements InputFilterAwareInterface
{
	public $produto_id;
	public $produto_nome;
	public $produto_preco;
	public $produto_foto;
	public $produto_descricao;
	public $produto_status;
	
	protected $inputFilter;
	
	public function exchangeArray($data) {		
		$this->produto_id = (isset($data['produto_id'])) ? $data['produto_id'] : null;
		$this->produto_nome = (isset($data['produto_nome'])) ? $data['produto_nome'] : null;
		$this->produto_preco = (isset($data['produto_preco'])) ? $data['produto_preco'] : null;
		if (isset($data['produto_foto']['name'])) {
			$this->produto_foto = $data['produto_foto']['name'];
		} else if (isset($data['produto_foto'])) {
			$this->produto_foto = $data['produto_foto'];
		} else {
			$this->produto_foto = null;
		}
		
		$this->produto_descricao = (isset($data['produto_descricao'])) ? $data['produto_descricao'] : null;
		$this->produto_status = (isset($data['produto_status'])) ? $data['produto_status'] : null;
	}
	
	public function getArrayCopy() {
		return get_object_vars($this);
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }
	
	public function getInputFilter() {
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory     = new InputFactory();
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'produto_id',
				'required'=> false,
				'filters' => array(
					array('name' => 'Int'),
				),		
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'produto_nome',
				'required' => true,
				'filters' => array(
						array('name' => 'StripTags'),
						array('name' => 'StringTrim')
				), 
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'options' => array(
							'messages' => array(								
								'isEmpty' => 'O campo nome é obrigatório'								
							)
						)
					),
				)						
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'produto_preco',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim')
				),
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'options' => array(
							'messages' => array(								
								'isEmpty' => 'O campo preço é obrigatório'									
							)
						)
					)
				)			
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'produto_foto',
				'required' => false					
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'produto_descricao',
				'required' => true,
				'filters' => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim')
				),
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'options' => array(
							'messages' => array(								
								'isEmpty' => 'O campo descrição é obrigatório'								
							)
						)
					),
					array(
						'name' => 'StringLength',
						true,
						'options' => array(
							'ecoding' => 'UTF-8',
							'min' => 10,
							'max' => 500,
							'message' => 'A descrição do produto deve ter entre 10 e 500 caracteres'
						)	
					)
				)					
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name' => 'produto_status',
				'required' => true,
				'filters' => array(
						array('name' => 'StripTags'),
						array('name' => 'StringTrim')
				)
			)));
			
			$this->inputFilter = $inputFilter;
			
		} 
		
		return $this->inputFilter;				
	}
}