<?php


namespace ZineInc\StorageBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use ZineInc\Storage\Client\Url;
use ZineInc\Storage\Common\ChecksumChecker;
use ZineInc\StorageBundle\Form\DataTransformer\FileDataTransformer;

class FileType extends AbstractType
{
    private $formConfig;
    private $endpointUrl;
    private $checksumChecker;

    public function __construct(array $formConfig, Url $endpointUrl, ChecksumChecker $checksumChecker)
    {
        $this->formConfig = $formConfig;
        $this->endpointUrl = $endpointUrl;
        $this->checksumChecker = $checksumChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new FileDataTransformer($this->checksumChecker))
            ->setAttribute('swf', $options['swf'])
            ->setAttribute('xap', $options['xap'])
            ->setAttribute('file_key', $options['file_key'])
            ->setAttribute('file_types', $options['file_types'])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['swf'] = $form->getConfig()->getAttribute('swf');
        $view->vars['xap'] = $form->getConfig()->getAttribute('xap');
        $view->vars['file_key'] = $form->getConfig()->getAttribute('file_key');
        $view->vars['file_types'] = $form->getConfig()->getAttribute('file_types');
        $view->vars['url'] = $this->endpointUrl;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $options = array(
            'compound' => false,
            'swf' => $this->formConfig['swf'],
            'xap' => $this->formConfig['xap'],
            'file_key' => $this->formConfig['file_key'],
            'file_types' => $this->formConfig['file_types'],
            'data_class' => 'ZineInc\\Storage\\Common\\FileId',
        );

        $resolver->setDefaults($options);
        $resolver->setAllowedTypes(array(
            'file_types' => 'array',
        ));
        $resolver->setNormalizers(array(
            'file_types' => function(Options $options, $values) {
                foreach($values as $key => $value) {
                    if(!isset($value['name']) || !is_string($value['name'])) {
                        throw new InvalidOptionsException(sprintf('Invalid file_types option. "%s" file type has no "name" value or "name" value is not a string', $key));
                    }

                    if(!isset($value['extensions']) || !is_array($value['extensions'])) {
                        throw new InvalidOptionsException(sprintf('Invalid file_types option. "%s" file type has no "extensions" value or "extensions" value is not an array', $key));
                    }
                }

                return $values;
            },
        ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'storage_file';
    }
}