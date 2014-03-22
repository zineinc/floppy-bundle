Quick tour
==========

1) Configure your storage server (shown in storage-server repo)

2) Configure this bundle:


    ...config.yml
    zine_inc_storage:
        endpoint:
            protocol: http
            host: your-storage-server-host.com
            path: /
        secret_key: your-super-secret-key-the-same-key-as-in-server

    #add form theme for form fields defined by this bundle
    twig:
      form:
        resources:
          - "ZineIncStorageBundle::form.html.twig"


3) Define your form model or entity:


    namespace ...

    use ZineInc\Storage\Common\FileId;
    use Doctrine\ORM\Mapping as ORM;

    class Document {
        /**
         * @ORM\Column(type='storage_file')
         */
        private $file;

        public function setFile(FileId $fileId = null) {
            $this->file = $fileId;
        }

        public function getFile() {
            return $fileId;
        }
    }

4) Use `storage_file` in your form type:


    class DocumentFormType extends .... {
        public function buildForm(...) {
            $builder->add('file', 'storage_file');
        }

        //...rest ommited
    }

5) Create your action to handle this form

6) In action template you should add css and javascripts for this form:


    {% block stylesheets %}
        {{ parent() }}
        {# default styles for form field #}
        <link href="{{ asset("bundles/zineincstorage/css/style.css") }}" rel="stylesheet" type="text/css" media="all" />
    {% endblock %}

    {% block javascripts %}
        {{ parent() }}

        {# jquery is required #}
        <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>

        {# default supported javascript library to file uploading is plupload #}
        <script src="http://rawgithub.com/moxiecode/plupload/master/js/plupload.full.min.js"></script>

        {# bundle specific javascript #}
        <script src="{{ asset("bundles/zineincstorage/js/StorageFileFormType.js") }}"></script>
    {% endblock %}

    {# block content #}
        {# render form #}
        {{ form_widget(form) }}
        {# ... #}
    {# endblock #}

7) Use your already created entity object and render file url:


    {# document variable is object of Document class defined in step 3 #}
    {{ storage_url(document.fileId.variant({ "name": "some name" }), "file") }}

    {# if you know the file is image you can render url to image with given sizes #}
    {{ storage_url(document.fileId.variant({ "width": 200, "height": 300 }), "image") }}
