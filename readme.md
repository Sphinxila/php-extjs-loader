# Sencha Architect ExtJS Project Loader #

This library allows you to load architect projects directly into your application without
loading the js files separately. 

The supplied internal project parser is resolving
all conflicts and provide the sorted code.


#### Extending the internal parser ####
You can easly extend the internal project parser

    \ExtJSLoader\ProjectParser::registerParser((
        new Parser()
    ));

#### Dump the project files for remote deployment without any *.xds files or metadata ####
If you provide the application for external customers and you don't want to deploy your project files, you can 
load  project from a 'compiled' (dump) file.

    new \ExtJSLoader\Project(
        "TestArchitectProject",                     //  Application name
        __DIR__ . "/../test/TestArchitectProject",  //  Root directory
        __DIR__ . "/TestCompiledProject.xvt",       //  Compiled path!
        "test-destination"                          //  Target div (render destination)
    );

    // Use compiled project if exists
    $this->loader->load(true, true);
    
    // Get code
    echo $this->loader->getCode();

## Examples ##
Easy implementation
- [Load architect project](example/TestProject.php)
- [Load compiled architect project](example/TestCompiledProject.php)
- [Extend loader with a custom parser](example/TestExtendedParserProject.php)
- [Minify / Compress JS output](example/TestCompressedProject.php)

## Installation ##
To use this library you need to add the following in your composer.json

    sphinxila/php-extjs-loader

## License / Copying ##

This project is released under the GPL v3 license, so feel free to share
or modify it.

## Bug report ##
To get a faster bug resolvement please provide an example code.