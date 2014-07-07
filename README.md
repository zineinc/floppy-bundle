# FloppyBundle

[![Build Status](https://travis-ci.org/zineinc/floppy-bundle.svg?branch=master)](https://travis-ci.org/zineinc/floppy-bundle)

FloppyBundle provides few additional integration points with [FloppyServer][2] library. Before reading this documentation, **you
should read documentation** for [**FloppyServer**][2] and [**FloppyClient**][3] first.

The main goal of Floppy-family libraries is to make dealing with every aspect of files as easy as possible. Floppy-family
libraries improve dealing with: file uploading, file storing, generating various file versions (thumbnails, watermarks etc.),
representing files in Entities and more.

# Documentation

## ToC

* [Quick tour](#quick-tour)
* [Integration points](#integration-points)
    * [Form](#form)
    * [Doctrine column type](#doctrine)
    * [Twig](#twig)
* [Configuration](#configuration)
* [License](#license)

<a name="quick-tour"></a>
## Quick tour

1) Configure your floppy server (shown in [floppy-server doc][4])

2) Add floppy/bundle to your composer and register this bundle in AppKernel:

composer.json (stable version is recommended, "*" as a version is only an example).

```

    "require": {
        ...
        "zineinc/floppy-bundle": "*"
        ...
    }

```

app/AppKernel.php:

```php

    public function registerBundles() {
        return array(
            //...
            new Floppy\Bundle\FloppyBundle(),
            //...
        );
    }

```

<a name="config-yml"></a>
3) Configure this bundle:

```yml

    ...config.yml
    floppy:
        endpoint:
            protocol: http
            host: your-floppy-server-host.com
            path: /
        secret_key: your-super-secret-key-the-same-key-as-in-server
        #similar syntax as in LiipImagineBundle / AvalancheImagineBundle
        #the same filters and options available as in LiipImagineBundle
        filter_sets:
            some_thumbnail:
                quality: 95
                thumbnail:
                    size: [50, 50]

    #add form theme for form fields defined by this bundle
    twig:
      form:
        resources:
          - "FloppyBundle::form.html.twig"

    ...routing.yml
    FloppyBundle:
        resource: "@FloppyBundle/Resources/config/routing.yml"
        prefix: "/floppy"
        
```
        
And that's all, next points explain how to use features of this bundle.

4) Define your form model or entity:

```php

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

```

5) Use `floppy_file` in your form type:

```php

    class DocumentFormType extends .... {
        public function buildForm(...) {
            $builder->add('file', 'floppy_file');
        }

        //...rest ommited
    }

```

6) Create your action to handle this form

7) In action template you should add css and javascripts for this form:

```html

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

```

8) Use your already created entity object and render file url:

```twig

    {# document variable is object of Document class defined in step 3 #}
    {{ floppy_url(document.fileId.with( { "name": "some name" })) }}

    {# if you know the file is image you can render url to thumbnail with given sizes #}
    {{ floppy_url(document.fileId.with({ "thumbnail": { "size": [80, 80] } }), "image") }}
    
    {# you can use filter set defined in app/config/config.yml file in ["filter_sets" section](#config-yml) #}
    {{ floppy_url(document.fileId|floppy_filter("some_thumbnail")}, "image") }}
    
    {# add custom options to filter set - for example add thumbnail mode #}
    {{ floppy_url(document.fileId|floppy_filter("some_thumbnail", { "thumbnail": { "mode": "inset" } } )}, "image") }}
    
```

<a name="integration-points"></a>
## Integration points

[FloppyClient][3] library adds two integration points with [FloppyServer][2]:

- url generation for files stored on FloppyServer
- client for file upload on FloppyServer

FloppyBundle adds additional 3 integration points:

- form type: floppy_file
- doctrine column type: floppy_file
- twig floppy_url() function and floppy_filter filter

<a name="form"></a>
### Form

**floppy_file** form type allows you to upload and assign file to your entity object. The form type uses [plupload][1] 
library as javascript uploader by default. To set up floppy_file, you should include floppy styles (css file), jquery,
plupload and FloppyFileFormType.js in your layout.

Example:

```php

    //form definition
    class DocumentFormType extends .... {
        public function buildForm(...) {
            $builder->add('file', 'floppy_file');
        }

        //...rest ommited
    }

```

```html

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

```

```yml

    #app/config/config.yml file
    
    #enable form theme
    twig:
      form:
        resources:
          - "FloppyBundle::form.html.twig"

    #app/config/routing.yml file
    
    #enable routing for floppy - it is used to generate file previews
    FloppyBundle:
        resource: "@FloppyBundle/Resources/config/routing.yml"
        prefix: "/floppy"
```

floppy_file has few options, the most important are:

- file_types - what file types that will be visible in file chooser dialog. This file types don't cause validation on 
server side, to validate file type you should use credentials. File types in this context are not exactly the same as
file handler names (more about file handlers you can read in FloppyServer and FloppyClient docs). File types have 
following structure: { name: "Human readable name", extensions: [ "txt" ] }. You can define aliases to predefined file types
in bundle configuration (floppy.form.file_type_aliases option, see more in "Configuration" section)
- transport_types - what javascript file transport should be used. Allowed values: html5, flash, silverlight and html4.
Default value are all in the order as mentioned before. The order is important, because when browser doesn't support for 
example html5 transport, next transport on the list will be used.
- credentials - credentials for file uploading. Supported options for credentials are: expiration (upload request 
expiration timestamp), file_types (allowed file types - it will be validated on server side), access (public/private, should file
be stored in public or in private storage). More about credentials you can find in FloppyServer and FloppyClient docs.

<a name="doctrine"></a>
### Doctrine column type

**floppy_file** column type simplify dealing with files in your entities. File in entity is represented by 
**Floppy\Common\FileId** object. The most interesting properties of FileId are:

- id - file hash + file extension, example: 5c7cd2fd39958b18a79f3c7e504d7cb2.jpg
- info - information about file (file size, mime type, image dimensions etc.), it only contains this information when FileId
comes from "floppy_file" form or `Floppy\Client\FloppyClient`::`upload`() method, so you can in setter method store this
additional info in other properties of entity

Example:

```php

    namespace ...

    use Floppy\Common\FileId;
    use Doctrine\ORM\Mapping as ORM;

    class Document {
        /**
         * @ORM\Column(type='floppy_file')
         */
        private $file;
        
        /**
         * @ORM\Column(type='string', length=50)
         */
        private $mimeType;

        public function setFile(FileId $fileId = null) {
            $this->file = $fileId;
            
            //you can store additional info in custom properties
            if($fileId !== null && $fileId->info()->get('mime-type')) {
                $this->mimeType = $fileId->info()->get('mime-type');
            }
        }

        public function getFile() {
            return $fileId;
        }
    }

```

<a name="twig"></a>
### Twig

After upload file (thanks to floppy_file form type) and store it in your entity (thanks to floppy_file doctrine column 
type) you may want to display url to the file. **floppy_url** twig function is able to generate url to the file.

Examples of floppy_url usage:

```twig

    {# url to original file #}
    {{ floppy_url(document.file) }}
    
    {# url to thumbnail, we assume the file is image #}
    {{ floppy_url(document.file.with({ "thumbnail": { "size": [50, 50] } })) }}
    {# as before, but file type is passed explicitly #}
    {{ floppy_url(document.file.with({ "thumbnail": { "size": [50, 50] } }), "image") }}
    
    {# add credentials to url #}
    {{ floppy_url(document.file, { "expiration": date().timestamp + 60 }) }}
    
    {# floppy_url with all arguments: file, file type and credentials #}
    {{ floppy_url(document.file.with({ "name": "some name" }), "file", { "expiration": date().timestamp + 60 }) }}
    
    {# if you want to use filter set definied in your app/config/config.yml file, you should use floppy_filter twig filter #}
    {{ floppy_url(document.file|floppy_filter("some_thumbnail")) }}
    
    {# filter set + credentials #}
    {{ floppy_url(document.file|floppy_filter("some_thumbnail"), { "expiration": date().timestamp + 60 }) }}
    
    {# filter set + custom options #}
    {{ floppy_url(document.fileId|floppy_filter("some_thumbnail", { "thumbnail": { "mode": "inset" } } )}) }}

```

<a name="configuration"></a>
## Configuration

Only two options are required: floppy.endpoint.host and floppy.secret_key. There is a list of all configuration options:

```yml

    floppy:
        endpoint:
            #Required
            host: ~
            protocol: http
            path: ""
            
        filter_sets:
            #default filter set for image preview in floppy_file form type. It is always automatically added even if you
            #overwrite filter_sets
            _preview:
                quality: 95
                thumbnail:
                    size: [80, 80]
            
        #Required
        #secret key that is used as salt to generate checksums, this value should be the same as in FloppyServer
        secret_key: ~
            
        #checksum is used for security checks, -1 means full checksum will be used in security checks
        checksum_length: -1
        
        default_credentials:
            #credentials for upload (used by floppy_file form and FloppyClient class) that will be used if credentials are empty
            upload: {}
            #credentials for download (used by floppy_url twig function) that will be used when credentials are empty
            download: {}
            
        #configuration for filepath generator. This values should be the same as in FloppyServer
        filepath_chooser:
            dir_count: 2
            chars_for_dir: 3
            orig_file_dir: orig
            variant_file_dir: v
        
        #name of the file post variable. This value should be the same as in FloppyServer, you shouldn't probably change this value
        file_key: file
        
        #enable doctrine floppy_file column or not
        enable_doctrine_file_type: true
        
        #name of doctrine column, you can change it
        doctrine_file_type_name: floppy_file
        
        #extensions for file types (this should be compatible with FloppyServer configuration, "file" file type can be omitted)
        #values for "image" will be automatically used in floppy.form.file_type_aliases.image.extensions and floppy.form.preview.image.supported_extensions,
        #so you don't have to repeat this values
        file_type_extensions: { image: [ "jpg", "jpeg", "png", "gif" ] }
        
        form:
            #aliases for file_types form option
            file_type_aliases: { image: { name: "Images", extensions: [ "jpg", "jpeg", "png", "gif" ] } }
            
            #urls for flash and silverlight scripts
            plupload:
                swf: %%request_base_path%%/bundles/floppy/plupload/Moxie.swf
                xap: %%request_base_path%%/bundles/floppy/plupload/Moxie.xap
                
            #configuration for file previews
            preview:
                image:
                    #filter set used to generate image preview in form
                    filter_set: "_preview"
                    #by default as same as floppy.file_type_extensions.image
                    supported_extensions: [ "jpg", "jpeg", "png", "gif" ]
                file:
                    name: "n-a"

```

<a name="license"></a>
## License

This project is under **MIT** license.

[1]: http://www.plupload.com/docs/
[2]: https://github.com/zineinc/floppy-server
[3]: https://github.com/zineinc/floppy-client
[4]: https://github.com/zineinc/floppy-server#setups
