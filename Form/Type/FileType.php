<?php


namespace Floppy\Bundle\Form\Type;


use Floppy\Client\Security\CredentialsGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Floppy\Client\Url;
use Floppy\Common\ChecksumChecker;
use Floppy\Bundle\Form\DataTransformer\FileDataTransformer;

class FileType extends AbstractType
{
    private $formConfig = array(
        'swf' => null,
        'xap' => null,
        'file_key' => null,
    );
    private $endpointUrl;
    private $checksumChecker;
    private $credentialsGenerator;
    private $fileTypeAliases = array();

    public function __construct(array $formConfig, Url $endpointUrl, ChecksumChecker $checksumChecker, CredentialsGenerator $credentialsGenerator, array $fileTypeAliases = array())
    {
        if($extraKeys = array_diff_key($formConfig, $this->formConfig)) {
            throw new \InvalidArgumentException(sprintf('Unexpected formConfig keys: %s', implode(', ', array_keys($extraKeys))));
        }

        $this->formConfig = $formConfig;
        $this->endpointUrl = $endpointUrl;
        $this->checksumChecker = $checksumChecker;
        $this->credentialsGenerator = $credentialsGenerator;

        $this->validateFileTypes($fileTypeAliases);
        $this->fileTypeAliases = $fileTypeAliases;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new FileDataTransformer($this->checksumChecker))
            ->setAttribute('swf', $options['swf'])
            ->setAttribute('xap', $options['xap'])
            ->setAttribute('file_key', $options['file_key'])
            ->setAttribute('file_types', $options['file_types'])
            ->setAttribute('transport_types', $options['transport_types'])
            ->setAttribute('credentials', $options['credentials'])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['swf'] = $form->getConfig()->getAttribute('swf');
        $view->vars['xap'] = $form->getConfig()->getAttribute('xap');
        $view->vars['file_key'] = $form->getConfig()->getAttribute('file_key');
        $view->vars['file_types'] = $form->getConfig()->getAttribute('file_types');
        $view->vars['url'] = $this->endpointUrl;
        $view->vars['transport_types'] = $form->getConfig()->getAttribute('transport_types');

        $credentials = $form->getConfig()->getAttribute('credentials');
        $view->vars['credentials'] = $this->credentialsGenerator->generateCredentials($credentials);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $options = array(
            'compound' => false,
            'swf' => $this->formConfig['swf'],
            'xap' => $this->formConfig['xap'],
            'file_key' => $this->formConfig['file_key'],
            'file_types' => array(),
            'transport_types' => array('html5', 'flash', 'silverlight', 'html4'),
            'credentials' => array(),
        );

        $resolver->setDefaults($options);
        $resolver->setAllowedTypes(array(
            'file_types' => array('string', 'array'),
            'credentials' => array('array'),
        ));

        $formType = $this;
        $fileTypeAliases = $this->fileTypeAliases;

        $resolver->setNormalizers(array(
            'file_types' => function(Options $options, $values) use($formType, $fileTypeAliases) {
                if(is_string($values)) {
                    $values = array($values);
                }
                foreach($values as $key => $value) {
                    if(is_string($value) && isset($fileTypeAliases[$value])) {
                        $values[$key] = $fileTypeAliases[$value];
                    }
                }

                try {
                    $formType->validateFileTypes($values);
                } catch(\InvalidArgumentException $e) {
                    throw new InvalidOptionsException($e->getMessage(), $e->getCode(), $e);
                }

                return $values;
            },
        ));
    }

    public function validateFileTypes($fileTypes)
    {
        foreach($fileTypes as $key => $value) {
            if(!isset($value['name']) || !is_string($value['name'])) {
                throw new \InvalidArgumentException(sprintf('Invalid file_types option. "%s" file type has no "name" value or "name" value is not a string', $key));
            }

            if(!isset($value['extensions']) || !is_array($value['extensions'])) {
                throw new \InvalidArgumentException(sprintf('Invalid file_types option. "%s" file type has no "extensions" value or "extensions" value is not an array', $key));
            }
        }
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'floppy_file';
    }
}