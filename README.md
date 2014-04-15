Quick tour
==========

1) Configure your floppy server (shown in floppy-server repo)

2) Configure this bundle:


    ...config.yml
    floppy:
        endpoint:
            protocol: http
            host: your-floppy-server-host.com
            path: /
        secret_key: your-super-secret-key-the-same-key-as-in-server

    #add form theme for form fields defined by this bundle
    twig:
      form:
        resources:
          - "FloppyBundle::form.html.twig"

    ...routing.yml
    FloppyBundle:
        resource: "@FloppyBundle/Resources/config/routing.yml"
        prefix: "/floppy"


3) Define your form model or entity:


    namespace ...

    use Floppy\Common\FileId;
    use Doctrine\ORM\Mapping as ORM;

    class Document {
        /**
         * @ORM\Column(type='floppy_file')
         */
        private $file;

        public function setFile(FileId $fileId = null) {
            $this->file = $fileId;
        }

        public function getFile() {
            return $fileId;
        }
    }

4) Use `floppy_file` in your form type:


    class DocumentFormType extends .... {
        public function buildForm(...) {
            $builder->add('file', 'floppy_file');
        }

        //...rest ommited
    }

5) Create your action to handle this form

6) In action template you should add css and javascripts for this form:


    {% block stylesheets %}
        {{ parent() }}
        {# default styles for form field #}
        <link href="{{ asset("bundles/floppy/css/style.css") }}" rel="stylesheet" type="text/css" media="all" />
    {% endblock %}

    {% block javascripts %}
        {{ parent() }}

        {# jquery is required #}
        <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>

        {# default supported javascript library to file uploading is plupload #}
        <script src="http://rawgithub.com/moxiecode/plupload/master/js/plupload.full.min.js"></script>

        {# bundle specific javascript #}
        <script src="{{ asset("bundles/floppy/js/FloppyFileFormType.js") }}"></script>
    {% endblock %}

    {# block content #}
        {# render form #}
        {{ form_widget(form) }}
        {# ... #}
    {# endblock #}

7) Use your already created entity object and render file url:


    {# document variable is object of Document class defined in step 3 #}
    {{ floppy_url(document.fileId, { "name": "some name" }) }}

    {# if you know the file is image you can render url to image with given sizes #}
    {{ floppy_url(document.fileId, { "width": 200, "height": 300 }, "image") }}
