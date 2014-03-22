<?php


namespace ZineInc\StorageBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
            ->setAttribute('js', $options['js'])
            ->setAttribute('swf', $options['swf'])
            ->setAttribute('xap', $options['xap'])
            ->setAttribute('file_key', $options['file_key'])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['js'] = $form->getConfig()->getAttribute('js');
        $view->vars['swf'] = $form->getConfig()->getAttribute('swf');
        $view->vars['xap'] = $form->getConfig()->getAttribute('xap');
        $view->vars['file_key'] = $form->getConfig()->getAttribute('file_key');
        $view->vars['url'] = $this->endpointUrl;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $options = array(
            'compound' => false,
            'js' => $this->formConfig['js'],
            'swf' => $this->formConfig['swf'],
            'xap' => $this->formConfig['xap'],
            'file_key' => $this->formConfig['file_key'],
            'data_class' => 'ZineInc\\Storage\\Common\\FileId',
        );

        $resolver->setDefaults($options);
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