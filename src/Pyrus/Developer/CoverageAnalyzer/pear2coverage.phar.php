<?php
function __autoload($class)
{
    $class = str_replace("Pyrus\Developer\CoverageAnalyzer\\", "", $class);
    var_dump($class);
    include "phar://" . __FILE__ . "/" . str_replace("\\", "/", $class) . ".php";
}
Phar::webPhar("pear2coverage.phar.php");
echo "This phar is a web application, run within your web browser to use\n";
exit -1;
__HALT_COMPILER(); ?>
�  &                  PEAR2    d'N        �         PEAR2/Templates    d'N        �         PEAR2/Templates/Savant    d'N        �      1   PEAR2/Templates/Savant/BadMethodCallException.php   d'N   p����      ,   PEAR2/Templates/Savant/BasicFastCompiler.php�  d'N�  �<�      0   PEAR2/Templates/Savant/ClassToTemplateMapper.php�  d'N�  J�Y��      ,   PEAR2/Templates/Savant/CompilerException.phpn   d'Nn   �|ϧ�      ,   PEAR2/Templates/Savant/CompilerInterface.phpo   d'No   ���      $   PEAR2/Templates/Savant/Exception.phpD   d'ND   J1�      0   PEAR2/Templates/Savant/FastCompilerInterface.phpg   d'Ng   �YKS�      )   PEAR2/Templates/Savant/FilterAbstract.php�  d'N�   ��^�         PEAR2/Templates/Savant/Main.php�h  d'N�h  /����      *   PEAR2/Templates/Savant/MapperInterface.php`   d'N`   @��̶      "   PEAR2/Templates/Savant/ObjectProxy    d'N        �      2   PEAR2/Templates/Savant/ObjectProxy/ArrayAccess.phpB  d'NB  �Y�Ŷ      4   PEAR2/Templates/Savant/ObjectProxy/ArrayIterator.php�  d'N�  �c�ض      2   PEAR2/Templates/Savant/ObjectProxy/ArrayObject.php�  d'N�  -�1�      2   PEAR2/Templates/Savant/ObjectProxy/Traversable.phpQ  d'NQ  ,r�p�      &   PEAR2/Templates/Savant/ObjectProxy.php  d'N  �sg\�      ,   PEAR2/Templates/Savant/TemplateException.phpn   d'Nn   ��N�      3   PEAR2/Templates/Savant/UnexpectedValueException.php�   d'N�   mJ�z�         Web/Aggregator.php�  d'N�  u�7�         Web/ClassToTemplateMapper.php�  d'N�  ����         Web/Controller.php�  d'N�  `�+}�         Web/Exception.phpb   d'Nb   T0:�         Web/LineSummary.php  d'N  �aOv�         Web/SelectDatabase.php�   d'N�   �S臨         Web/Summary.php�  d'N�  �p��         Web/TestCoverage.php�  d'N�  Y�պ�         Web/TestSummary.php�  d'N�  %ؠ�         Web/View.phpB  d'NB  �o޶         SourceFile.php�  d'N�  ��2�         Aggregator.php  d'N  )�;߶         Exception.php^   d'N^   �e\j�      
   Sqlite.php�  d'N�  �w*�         SourceFile/PerTest.php  d'N  ����      	   cover.css�  d'N�  3�ʶ      	   index.php�  d'N�  �����      <?php
namespace PEAR2\Templates\Savant;

class BadMethodCallException extends \BadMethodCallException implements Exception
{

}<?php

namespace PEAR2\Templates\Savant;

class BasicFastCompiler implements FastCompilerInterface
{
    /**
     * Directory where compiled templates will be stored
     * 
     * @var string
     */
    protected $compiledtemplatedir;

    /**
     * Constructor for the BasicFastCompiler
     * 
     * @param string $compiledtemplatedir Where to store compiled templates
     * 
     * @throws UnexpectedValueException
     */
    function __construct($compiledtemplatedir)
    {
        $this->compiledtemplatedir = realpath($compiledtemplatedir);
        if (!$this->compiledtemplatedir && !is_writable($this->compiledtemplatedir)) {
            throw new UnexpectedValueException('Unable to compile templates into ' .
                                               $compiledtemplatedir . ', directory does not exist ' .
                                               'or is unwritable');
        }
        $this->compiledtemplatedir .= DIRECTORY_SEPARATOR;
    }

    /**
     * Compile a template.
     * 
     * @param string $name   Template to compile
     * @param Main   $savant Savant main object
     *
     * @return string Name of the compiled template file.
     */
    function compile($name, $savant)
    {
        $cname = $this->compiledtemplatedir . md5($name);
        if (file_exists($cname)) {
            if (filemtime($name) == filemtime($cname)) {
                return $cname;
            }
        }
        $a = file_get_contents($name);
        $a = "<?php return '" . str_replace(array('<?php echo', '?>'), array('\' . ', ' . \''), $a) . "';";
        file_put_contents($cname, $a);
        touch($cname, filemtime($name));
        return $cname;
    }
}<?php
/**
 * PEAR2\Templates\Savant\ClassToTemplateMapper
 *
 * PHP version 5
 *
 * @category  Templates
 * @package   PEAR2_Templates_Savant
 * @author    Brett Bieber <saltybeagle@php.net>
 * @copyright 2009 Brett Bieber
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   SVN: $Id$
 * @link      https://github.com/pear2/PEAR2_Templates_Savant
 */

/**
 * ClassToTemplateMapper class for PEAR2_Templates_Savant
 * 
 * This class allows class names to be mapped to template names though a simple
 * scheme.
 *
 * @category  Templates
 * @package   PEAR2_Templates_Savant
 * @author    Brett Bieber <saltybeagle@php.net>
 * @copyright 2009 Brett Bieber
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/PEAR2_Templates_Savant
 */
namespace PEAR2\Templates\Savant;
class ClassToTemplateMapper implements MapperInterface
{
    /**
     * Default template mapping can be temporarily overridden by 
     * assigning a direct template name.
     * 
     * ClassToTemplateMapper::$output_template['My_Class'] = 'My/Class_rss.tpl.php';
     * 
     * @var array
     */
    static $output_template       = array();
    
    /**
     * What character to use as a directory separator when mapping class names
     * to templates.
     * 
     * @var string
     */
    static $directory_separator   = '_';
    
    /**
     * Strip something out of class names before mapping them to templates.
     * 
     * This can be useful if your class names are very long, and you don't
     * want empty subdirectories within your templates directory.
     * 
     * @var string
     */
    static $classname_replacement = '';
    
    /**
     * The file extension to use
     * 
     * @var string
     */
    static $template_extension = '.tpl.php';
    
    /**
     * Maps class names to template filenames.
     * 
     * Underscores and namespace separators in class names are replaced with 
     * directory separators.
     * 
     * Examples:
     * Class           => Class.tpl.php
     * Other_Class     => Other/Class.tpl.php
     * namespace\Class => namespace/Class.tpl.php
     *
     * @param string $class Class name to map to a template
     * 
     * @return string Template file name
     */
    function map($class)
    {
        if (isset(static::$output_template[$class])) {
            $class = static::$output_template[$class];
        }
        
        $class = str_replace(array(static::$classname_replacement,
                                   static::$directory_separator,
                                   '\\'),
                             array('',
                                   DIRECTORY_SEPARATOR,
                                   DIRECTORY_SEPARATOR),
                             $class);
        
        $templatefile = $class . static::$template_extension;
        
        return $templatefile;
    }
    
}
?><?php
namespace PEAR2\Templates\Savant;

class CompilerException extends \Exception implements Exception {}
?><?php

namespace PEAR2\Templates\Savant;

interface CompilerInterface
{
    function compile($savant, $name);
}<?php
namespace PEAR2\Templates\Savant;

interface Exception
{

}
?><?php

namespace PEAR2\Templates\Savant;

interface FastCompilerInterface extends CompilerInterface
{
}<?php

/**
* 
* Abstract Savant3_Filter class.
* 
* @package Savant3
* 
* @author Paul M. Jones <pmjones@ciaweb.net>
* 
* @license http://www.gnu.org/copyleft/lesser.html LGPL
* 
* @version $Id: Filter.php,v 1.5 2005/04/29 16:23:50 pmjones Exp $
*
*/

/**
* 
* Abstract Savant3_Filter class.
*
* You have to extend this class for it to be useful; e.g., "class
* Savant3_Filter_example extends Savant3_Filter".
* 
* @package Savant3
* 
* @author Paul M. Jones <pmjones@ciaweb.net>
* 
*/
namespace PEAR2\Templates\Savant;
abstract class FilterAbstract
{
    
    
    /**
    * 
    * Optional reference to the calling Savant object.
    * 
    * @access protected
    * 
    * @var object
    * 
    */
    
    protected $savant = null;
    
    
    /**
    * 
    * Constructor.
    * 
    * @access public
    * 
    * @param array $conf An array of configuration keys and values for
    * this filter.
    * 
    * @return void
    * 
    */
    
    public function __construct($conf = null)
    {
        settype($conf, 'array');
        foreach ($conf as $key => $val) {
            $this->$key = $val;
        }
    }
    
    
    /**
    * 
    * Stub method for extended behaviors.
    *
    * @access public
    * 
    * @param string $text The text buffer to filter.
    *
    * @return string The text buffer after it has been filtered.
    *
    */
    
    public static function filter($text)
    {
        return $text;
    }
}
?><?php
/**
 * PEAR2\Templates\Savant\Main
 *
 * PHP version 5
 *
 * @category  Templates
 * @package   PEAR2_Templates_Savant
 * @author    Brett Bieber <saltybeagle@php.net>
 * @copyright 2009 Brett Bieber
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   SVN: $Id$
 * @link      https://github.com/pear2/PEAR2_Templates_Savant
 */

/**
 * Main class for PEAR2_Templates_Savant
 *
 * @category  Templates
 * @package   PEAR2_Templates_Savant
 * @author    Brett Bieber <saltybeagle@php.net>
 * @copyright 2009 Brett Bieber
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/PEAR2_Templates_Savant
 */
namespace PEAR2\Templates\Savant;
class Main
{
    /**
    * 
    * Array of configuration parameters.
    * 
    * @access protected
    * 
    * @var array
    * 
    */
    
    protected $__config = array(
        'compiler'      => null,
        'filters'       => array(),
        'escape'        => null,
    );
    
    /**
     * Parameters for escaping.
     * 
     * @var array
     */
    protected $_escape = array(
        'quotes'  => ENT_COMPAT,
        'charset' => 'UTF-8',
        );
    
    /**
     * The output template to render using
     * 
     * @var string
     */
    protected $template;

    /**
     * stack of templates, so we can access the parent template
     * 
     * @var array
     */
    protected $templateStack = array();

    /**
     * To avoid stats on locating templates, populate this array with
     * full path => 1 for any existing templates
     * 
     * @var array
     */
    protected $templateMap = array();
    
    /**
     * An array of paths to look for template files in.
     * 
     * @var array
     */
    protected $template_path = array('./');

    /**
     * A list of output controllers.  One does no filtering, another does.  This
     * makes non-filtering controllers faster.
     * 
     * @var array
     */
    protected $output_controllers = array();

    /**
     * The current controller to use
     * 
     * @var string
     */
    protected $selected_controller;
    
    /**
     * How class names are translated to templates
     * 
     * @var MapperInterface
     */
    protected $class_to_template;

    /**
     * Array of globals available within every template
     * 
     * @var array
     */
    protected $globals = array();
    // -----------------------------------------------------------------
    //
    // Constructor and magic methods
    //
    // -----------------------------------------------------------------
    
    
    /**
    * 
    * Constructor.
    * 
    * @access public
    * 
    * @param array $config An associative array of configuration keys for
    * the Main object.  Any, or none, of the keys may be set.
    * 
    * @return PEAR2\Templates\Savant\Main A PEAR2\Templates\Savant\Main instance.
    * 
    */
    
    public function __construct($config = null)
    {
        $savant = $this;
        $this->output_controllers['basic'] = function($context, $parent, $file) use ($savant) {
                foreach ($savant->getGlobals() as $__name => $__value) {
                    $$__name = $__value;
                }
                unset($__name, $__value);
                ob_start();
                include $file;
                return ob_get_clean();
            };
        $this->output_controllers['filter'] = function($context, $parent, $file) use ($savant) {
                foreach ($savant->getGlobals() as $__name => $__value) {
                    $$__name = $__value;
                }
                unset($__name, $__value);
                ob_start();
                include $file;
                return $savant->applyFilters(ob_get_clean());
            };
        $this->output_controllers['basiccompiled'] = function($context, $parent, $file) use ($savant) {
                foreach ($savant->getGlobals() as $__name => $__value) {
                    $$__name = $__value;
                }
                unset($__name, $__value);
                ob_start();
                include $savant->template($file);
                return ob_get_clean();
            };
        $this->output_controllers['filtercompiled'] = function($context, $parent, $file) use ($savant) {
                foreach ($savant->getGlobals() as $__name => $__value) {
                    $$__name = $__value;
                }
                unset($__name, $__value);
                ob_start();
                include $savant->template($file);
                return $savant->applyFilters(ob_get_clean());
            };
        $this->output_controllers['basicfastcompiled'] = function($context, $parent, $file) use ($savant) {
                foreach ($savant->getGlobals() as $__name => $__value) {
                    $$__name = $__value;
                }
                unset($__name, $__value);
                return include $savant->template($file);
            };
        $this->output_controllers['filterfastcompiled'] = function($context, $parent, $file) use ($savant) {
                foreach ($savant->getGlobals() as $__name => $__value) {
                    $$__name = $__value;
                }
                unset($__name, $__value);
                return $savant->applyFilters(include $savant->template($file));
            };
        $this->selected_controller = 'basic';
        
        // set the default template search path
        if (isset($config['template_path'])) {
            // user-defined dirs
            $this->setTemplatePath($config['template_path']);
        }
        
        // set the output escaping callbacks
        if (isset($config['escape'])) {
            $this->setEscape($config['escape']);
        }
        
        // set the default filter callbacks
        if (isset($config['filters'])) {
            $this->addFilters($config['filters']);
        }
    }

    /**
     * Add a global variable which will be available inside every template
     * 
     * @param string $var   The global variable name
     * @param mixed  $value The value
     * 
     * @return void
     */
    function addGlobal($name, $value)
    {
        switch ($name) {
            case 'context':
            case 'parent':
            case 'template':
            case 'file':
            case 'savant':
            case 'this':
                throw new BadMethodCallException('Invalid global variable name');
        }

        if ($this->__config['escape']) {
            switch (gettype($value)) {
                case 'object':
                    if (!$value instanceof ObjectProxy) {
                        $value = ObjectProxy::factory($value, $this);
                    }
                    break;
                case 'string':
                case 'int':
                case 'double':
                    $value = $this->escape($value);
                    break;
                case 'array':
                    $value = new ObjectProxy\ArrayIterator($value, $this);
                    break;
            }
        }

        $this->globals[$name] = $value;
    }

    /**
     * Get the array of assigned globals
     * 
     * @return array
     */
    function getGlobals()
    {
        return $this->globals;
    }

    /**
     * Return the current template set (if any)
     * 
     * @return string
     */
    function getTemplate()
    {
        return $this->template;
    }
    
    
    // -----------------------------------------------------------------
    //
    // Public configuration management (getters and setters).
    // 
    // -----------------------------------------------------------------
    
    
    /**
    *
    * Returns a copy of the Savant configuration parameters.
    *
    * @access public
    * 
    * @param string $key The specific configuration key to return.  If null,
    * returns the entire configuration array.
    * 
    * @return mixed A copy of the $this->__config array.
    * 
    */
    
    public function getConfig($key = null)
    {
        if (is_null($key)) {
            // no key requested, return the entire config array
            return $this->__config;
        } elseif (empty($this->__config[$key])) {
            // no such key
            return null;
        } else {
            // return the requested key
            return $this->__config[$key];
        }
    }
    
    
    /**
    * 
    * Sets a custom compiler/pre-processor callback for template sources.
    * 
    * By default, Savant does not use a compiler; use this to set your
    * own custom compiler (pre-processor) for template sources.
    * 
    * @access public
    * 
    * @param mixed $compiler A compiler callback value suitable for the
    * first parameter of call_user_func().  Set to null/false/empty to
    * use PHP itself as the template markup (i.e., no compiling).
    * 
    * @return void
    * 
    */
    
    public function setCompiler(CompilerInterface $compiler)
    {
        $this->__config['compiler'] = $compiler;
        if ($compiler instanceof FastCompilerInterface) {
            switch ($this->selected_controller) {
                case 'basic' :
                case 'basiccompiled';
                    $this->selected_controller = 'basicfastcompiled';
                    break;
                case 'filter' :
                case 'filtercompiled' :
                    $this->selected_controller = 'filterfastcompiled';
                    break;
            }
            return;
        }
        if (!strpos($this->selected_controller, 'compiled')) {
            $this->selected_controller .= 'compiled';
        }
    }
    
    /**
     * Set the class to template mapper.
     * 
     * @see MapperInterface
     * 
     * @param MapperInterface $mapper The mapper interface to use 
     * 
     * @return Main
     */
    function setClassToTemplateMapper(MapperInterface $mapper)
    {
        $this->class_to_template = $mapper;
        return $this;
    }
    
    /**
     * Get the class to template mapper.
     * 
     * @return MapperInterface
     */
    function getClassToTemplateMapper()
    {
        if (!isset($this->class_to_template)) {
            $this->setClassToTemplateMapper(new ClassToTemplateMapper());
        }
        return $this->class_to_template;
    }
    
    
    // -----------------------------------------------------------------
    //
    // Output escaping and management.
    //
    // -----------------------------------------------------------------
    
    
    /**
    * 
    * Clears then sets the callbacks to use when calling $this->escape().
    * 
    * Each parameter passed to this function is treated as a separate
    * callback.  For example:
    * 
    * <code>
    * $savant->setEscape(
    *     'stripslashes',
    *     'htmlspecialchars',
    *     array('StaticClass', 'method'),
    *     array($object, $method)
    * );
    * </code>
    * 
    * @access public
    *
    * @return Main
    *
    */
    
    public function setEscape()
    {
        $this->__config['escape'] = @func_get_args();
        return $this;
    }
    
    
    /**
    *
    * Gets the array of output-escaping callbacks.
    *
    * @access public
    *
    * @return array The array of output-escaping callbacks.
    *
    */
    
    public function getEscape()
    {
        return $this->__config['escape'];
    }
    
    
    /**
     * Escapes a value for output in a view script.
     *
     * If escaping mechanism is one of htmlspecialchars or htmlentities, uses
     * {@link $_encoding} setting.
     *
     * @param mixed $var The output to escape.
     * 
     * @return mixed The escaped value.
     */
    public function escape($var)
    {
        foreach ($this->__config['escape'] as $escape) {
            if (in_array($escape,
                    array('htmlspecialchars', 'htmlentities'), true)) {
                $var = call_user_func($escape,
                                      $var,
                                      $this->_escape['quotes'],
                                      $this->_escape['charset']);
            } else {
                $var = call_user_func($escape, $var);
            }
        }
        return $var;
    }
    
    
    // -----------------------------------------------------------------
    //
    // File management
    //
    // -----------------------------------------------------------------
    
    /**
     * Get the template path.
     * 
     * @return array
     */
    function getTemplatePath()
    {
        return $this->template_path;
    }
    
    /**
    *
    * Sets an entire array of search paths for templates or resources.
    *
    * @access public
    * 
    * @param string|array $path The new set of search paths.  If null or
    * false, resets to the current directory only.
    *
    * @return Main
    *
    */
    
    public function setTemplatePath($path = null)
    {
        // clear out the prior search dirs, add default
        $this->template_path = array('./');
        
        // actually add the user-specified directories
        $this->addTemplatePath($path);
        return $this;
    }
    
    
    /**
    *
    * Adds to the search path for templates and resources.
    *
    * @access public
    *
    * @param string|array $path The directory or stream to search.
    *
    * @return Main
    *
    */
    
    public function addTemplatePath($path)
    {
        // convert from path string to array of directories
        if (is_string($path) && !strpos($path, '://')) {
        
            // the path config is a string, and it's not a stream
            // identifier (the "://" piece). add it as a path string.
            $path = explode(PATH_SEPARATOR, $path);
            
            // typically in path strings, the first one is expected
            // to be searched first. however, Savant uses a stack,
            // so the first would be last.  reverse the path string
            // so that it behaves as expected with path strings.
            $path = array_reverse($path);
            
        } else {
        
            // just force to array
            settype($path, 'array');
            
        }
        
        // loop through the path directories
        foreach ($path as $dir) {
        
            // no surrounding spaces allowed!
            $dir = trim($dir);
            
            // add trailing separators as needed
            if (strpos($dir, '://')) {
                if (substr($dir, -1) != '/') {
                    // stream
                    $dir .= '/';
                }
            } elseif (substr($dir, -1) != DIRECTORY_SEPARATOR) {
                if (false !== strpos($dir, '..')) {
                    // checking for weird paths here removes directory traversal threat
                    throw new UnexpectedValueException('upper directory reference .. cannot be used in template path');
                }
                // directory
                $dir .= DIRECTORY_SEPARATOR;
            }

            // add to the top of the search dirs
            array_unshift(
                $this->template_path,
                $dir
            );
        }
    }
    
    
    /**
    * 
    * Searches the directory paths for a given file.
    * 
    * @param string $file The file name to look for.
    * 
    * @return string|bool The full path and file name for the target file,
    * or boolean false if the file is not found in any of the paths.
    *
    */
    
    public function findTemplateFile($file)
    {
        if (false !== strpos($file, '..')) {
            // checking for weird path here removes directory traversal threat
            throw new UnexpectedValueException('upper directory reference .. cannot be used in template filename');
        }
        
        // start looping through the path set
        foreach ($this->template_path as $path) {
            // get the path to the file
            $fullname = $path . $file;

            if (isset($this->templateMap[$fullname])) {
                return $fullname;
            }

            if (!@is_readable($fullname)) {
                continue;
            }

            return $fullname;
        }

        // could not find the file in the set of paths
        throw new TemplateException('Could not find the template ' . $file);
    }
    
    
    // -----------------------------------------------------------------
    //
    // Template processing
    //
    // -----------------------------------------------------------------
    
    /**
     * Render context data through a template.
     * 
     * This method allows you to render data through a template. Typically one
     * will pass the model they wish to display through an optional template.
     * If no template is specified, the ClassToTemplateMapper::map() method
     * will be called which should return the name of a template to render.
     * 
     * Arrays will be looped over and rendered through the template specified.
     * 
     * Strings, ints, and doubles will returned if no template parameter is 
     * present.
     * 
     * Within templates, two variables will be available, $context and $savant.
     * The $context variable will contain the data passed to the render method,
     * the $savant object will be an instance of the Main class with which you
     * can render nested data through partial templates.
     * 
     * @param mixed $mixed     Data to display through the template.
     * @param string $template A template to display data in.
     * 
     * @return string The template output
     */
    function render($mixed = null, $template = null)
    {
        $method = 'render'.gettype($mixed);
        return $this->$method($mixed, $template);
    }
    
    /**
     * Called when a resource is rendered
     * 
     * @param resource $resouce  The resources
     * @param string   $template Template
     * 
     * @return void
     * 
     * @throws UnexpectedValueException
     */
    protected function renderResource($resouce, $template = null)
    {
        throw new UnexpectedValueException('No way to render a resource!');
    }
    
    protected function renderBoolean($bool, $template = null)
    {
        return $this->renderString((string)$bool, $template);
    }
    
    protected function renderDouble($double, $template = null)
    {
        return $this->renderString($double, $template);
    }
    
    protected function renderInteger($int, $template = null)
    {
        return $this->renderString($int, $template);
    }
    
    /**
     * Render string of data
     * 
     * @param string $string   String of data
     * @param string $template A template to display the string in
     * 
     * @return string
     */
    protected function renderString($string, $template = null)
    {
        if ($this->__config['escape']) {
            $string = $this->escape($string);
        }
        
        if ($template) {
            return $this->fetch($string, $template);
        }

        if (!$this->__config['filters']) {
            return $string;
        }
        return $this->applyFilters($string);
    }
    
    /**
     * Used to render context array
     * 
     * @param array  $array    Data to render
     * @param string $template Template to render
     * 
     * @return string Rendered output
     */
    protected function renderArray(array $array, $template = null)
    {
        $savant = $this;
        $render = function($output, $mixed) use ($savant, $template) {
            return $output . $savant->render($mixed, $template);
        };
        return array_reduce($array, $render, '');
    }

    /**
     * Render an associative array of data through a template.
     * 
     * Three parameters will be passed to the closure, the array key, value,
     * and selective third parameter.
     * 
     * @param array   $array    Associative array of data
     * @param mixed   $selected Optional parameter to pass
     * @param Closure $template A closure that will be called
     * 
     * @return string
     */
    public function renderAssocArray(array $array, $selected = false, Closure $template)
    {
        $ret = '';
        foreach ($array as $key => $element) {
            $ret .= $template($key, $element, $selected);
        }
        return $ret;
    }

    protected function renderArrayAccess(\ArrayAccess $array, $template = null)
    {
        $ret = '';
        foreach ($array as $key => $element) {
            $ret .= $this->render($element, $template);
        }
        return $ret;
    }

    /**
     * Render an if else conditional template output.
     * 
     * @param mixed  $condition      The conditional to evaluate
     * @param mixed  $render         Context data to render if condition is true
     * @param mixed  $else           Context data to render if condition is false
     * @param string $rendertemplate If true, render using this template
     * @param string $elsetemplate   If false, render using this template
     * 
     * @return string
     */
    public function renderElse($condition, $render, $else, $rendertemplate = null, $elsetemplate = null)
    {
        if ($condition) {
            $this->render($render, $rendertemplate);
        } else {
            $this->render($else, $elsetemplate);
        }
    }
    
    /**
     * Used to render an object through a template.
     * 
     * @param object $object   Model containing data
     * @param string $template Template to render data through
     * 
     * @return string Rendered output
     */
    protected function renderObject($object, $template = null)
    {
        if ($this->__config['escape']) {

            if (!$object instanceof ObjectProxy) {
                $object = ObjectProxy::factory($object, $this);
            }

            if ($object instanceof ObjectProxy\ArrayIterator) {
                return $this->renderArrayAccess($object);
            }
        }
        return $this->fetch($object, $template);
    }
    
    /**
     * Used to render null through an optional template
     * 
     * @param null   $null     The null var
     * @param string $template Template to render null through
     * 
     * @return string Rendered output
     */
    protected function renderNULL($null, $template = null)
    {
        if ($template) {
            return $this->fetch(null, $template);
        }
    }
    
    protected function fetch($mixed, $template = null)
    {
        if ($template) {
            $this->template = $template;
        } else {
            if ($mixed instanceof ObjectProxy) {
                $class = $mixed->__getClass();
            } else {
                $class = get_class($mixed);
            }
            $this->template = $this->getClassToTemplateMapper()->map($class);
        }
        $current          = new \stdClass;
        $current->file    = $this->findTemplateFile($this->template);
        $current->context = $mixed;
        $current->parent  = null;
        $outputcontroller = $this->output_controllers[$this->selected_controller];
        if (count($this->templateStack)) {
            $current->parent = $this->templateStack[count($this->templateStack)-1];
        }
        $this->templateStack[] = $current;
        $ret = $outputcontroller($current->context, $current->parent, $current->file);
        array_pop($this->templateStack);
        return $ret;
    }
    
    /**
    *
    * Compiles a template and returns path to compiled script.
    * 
    * By default, Savant does not compile templates, it uses PHP as the
    * markup language, so the "compiled" template is the same as the source
    * template.
    *
    * If a compiler is specific, this method is used to look up the compiled
    * template script name
    *
    * @param string $tpl The template source name to look for.
    * 
    * @return string The full path to the compiled template script.
    * 
    * @throws PEAR2\Templates\Savant\UnexpectedValueException
    * @throws PEAR2\Templates\Savant\Exception
    * 
    */
    
    public function template($tpl = null)
    {
        // find the template source.
        $file = $this->findTemplateFile($tpl);
        
        // are we compiling source into a script?
        if ($this->__config['compiler']) {
            // compile the template source and get the path to the
            // compiled script (will be returned instead of the
            // source path)
            $result = $this->__config['compiler']->compile($file, $this);
        } else {
            // no compiling requested, use the source path
            $result = $file;
        }
        
        // is there a script from the compiler?
        if (!$result) {
            // return an error, along with any error info
            // generated by the compiler.
            throw new Exception('Compiler error for template '.$tpl.'. '.$result );
            
        } else {
            // no errors, the result is a path to a script
            return $result;
        }
    }
    
    
    // -----------------------------------------------------------------
    //
    // Filter management and processing
    //
    // -----------------------------------------------------------------
    
    
    /**
    * 
    * Resets the filter stack to the provided list of callbacks.
    * 
    * @access protected
    * 
    * @param array An array of filter callbacks.
    * 
    * @return void
    * 
    */
    
    public function setFilters()
    {
        $this->__config['filters'] = (array) @func_get_args();
        if (!$this->__config['filters']) {
            $this->selected_controller = 'basic';
        } else {
            $this->selected_controller = 'filter';
        }
    }
    
    
    /**
    * 
    * Adds filter callbacks to the stack of filters.
    * 
    * @access protected
    * 
    * @param array An array of filter callbacks.
    * 
    * @return void
    * 
    */
    
    public function addFilters()
    {
        // add the new filters to the static config variable
        // via the reference
        foreach ((array) @func_get_args() as $callback) {
            $this->__config['filters'][] = $callback;
            $this->selected_controller = 'filter';
        }
    }
    
    
    /**
    * 
    * Runs all filter callbacks on buffered output.
    * 
    * @access protected
    * 
    * @param string The template output.
    * 
    * @return void
    * 
    */
    
    public function applyFilters($buffer)
    {
        foreach ($this->__config['filters'] as $callback) {
            $buffer = call_user_func($callback, $buffer);
        }
        return $buffer;
    }
    
}
<?php

namespace PEAR2\Templates\Savant;

interface MapperInterface
{
    function map($name);
}<?php
namespace PEAR2\Templates\Savant\ObjectProxy;
use PEAR2\Templates\Savant\ObjectProxy;
class ArrayAccess extends ObjectProxy implements \ArrayAccess
{
    function offsetExists($offset)
    {
        return $this->object->offsetExists($offset);
    }
    
    function offsetGet($offset)
    {
        return $this->filterVar($this->object->offsetGet($offset));
    }
    
    function offsetSet($offset, $value)
    {
        $this->object->offsetSet($offset, $value);
    }
    
    function offsetUnset($offset)
    {
        $this->object->offsetUnset($offset);
    }
}<?php
namespace PEAR2\Templates\Savant\ObjectProxy;
use PEAR2\Templates\Savant\ObjectProxy;
class ArrayIterator extends ObjectProxy\ArrayAccess implements \Iterator, \SeekableIterator, \Countable 
{

    /**
     * Construct a new object proxy
     *
     * @param array $array  The array
     * @param Main  $savant The savant templating system
     */
    function __construct($array, $savant)
    {
        parent::__construct(new \ArrayIterator($array), $savant);
    }

    function current()
    {
        return $this->object->current();
    }

    function next()
    {
        return $this->object->next();
    }

    function key()
    {
        return $this->object->key();
    }

    function valid()
    {
        return $this->object->valid();
    }

    function rewind()
    {
        return $this->object->rewind();
    }

    function seek($offset)
    {
        return $this->object->seek($offset);
    }
}
<?php

/**
 * PEAR2\Templates\Savant\ObjectProxy\ArrayObject
 *
 * PHP version 5
 *
 * @category  Templates
 * @package   PEAR2_Templates_Savant
 * @author    Brett Bieber <saltybeagle@php.net>
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 Brett Bieber, 2011 Michael Gauthier
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   SVN: $Id$
 * @link      https://github.com/pear2/PEAR2_Templates_Savant
 */

/**
 * Proxies ArrayObject objects
 *
 * Filters on array access or on traversal.
 *
 * @category  Templates
 * @package   PEAR2_Templates_Savant
 * @author    Brett Bieber <saltybeagle@php.net>
 * @author    Michael Gauthier <mike@silverorange.com>
 * @copyright 2009 Brett Bieber, 2011 Michael Gauthier
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/PEAR2_Templates_Savant
 */
namespace PEAR2\Templates\Savant\ObjectProxy;
use PEAR2\Templates\Savant\ObjectProxy;
class ArrayObject
    extends ArrayAccess
    implements \ArrayAccess, \Countable, \Serializable, \IteratorAggregate
{
    public function getIterator()
    {
        return $this->object->getIterator();
    }

    public function next()
    {
        $this->object->next();
    }

    public function key()
    {
        return $this->object->key();
    }

    public function valid()
    {
        return $this->object->valid();
    }

    public function rewind()
    {
        $this->object->rewind();
    }

    public function current()
    {
        return $this->filterVar($this->object->current());
    }

    public function count()
    {
        return count($this->object);
    }

    public function serialize()
    {
        return serialize($this->object);
    }

    public function unserialize($string)
    {
        $object = unserialize($string);
        if ($object !== false) {
            $this->object = $object;
        }
    }
}
<?php
namespace PEAR2\Templates\Savant\ObjectProxy;
use PEAR2\Templates\Savant\ObjectProxy;
class Traversable extends ObjectProxy implements \Iterator
{

    function getIterator()
    {
        return $this->object;
    }

    function next()
    {
        $this->object->next();
    }

    function key()
    {
        return $this->object->key();
    }

    function valid()
    {
        return $this->object->valid();
    }

    function rewind()
    {
        $this->object->rewind();
    }

    function current()
    {
        return $this->filterVar($this->object->current());
    }
}<?php
/**
 * PEAR2\Templates\Savant\ObjectProxy
 *
 * PHP version 5
 *
 * @category  Templates
 * @package   PEAR2_Templates_Savant
 * @author    Brett Bieber <saltybeagle@php.net>
 * @copyright 2009 Brett Bieber
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   SVN: $Id$
 * @link      https://github.com/pear2/PEAR2_Templates_Savant
 */

/**
 * ObjectProxy class for PEAR2_Templates_Savant
 *
 * The ObjectProxy acts as an intermediary between an object and a template.
 * The $context variable will be an ObjectProxy which proxies member variable
 * access so escaping can be applied.
 *
 * @category  Templates
 * @package   PEAR2_Templates_Savant
 * @author    Brett Bieber <saltybeagle@php.net>
 * @copyright 2009 Brett Bieber
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link      https://github.com/pear2/PEAR2_Templates_Savant
 */
namespace PEAR2\Templates\Savant;
class ObjectProxy implements \Countable
{
    /**
     * The internal object
     *
     * @var mixed
     */
    protected $object;

    /**
     * The savant templating system
     *
     * @var PEAR2\Templates\Savant\Main
     */
    protected $savant;

    /**
     * Construct a new object proxy
     *
     * @param mixed $object The object
     * @param Main  $savant The savant templating system
     */
    function __construct($object, $savant)
    {
        $this->object = $object;
        $this->savant = $savant;
    }

    /**
     * Magic method for retrieving data.
     *
     * String data will be escaped with $savant->escape() before it is returned
     *
     * @return mixed
     */
    function __get($var)
    {
        return $this->filterVar($this->object->$var);
    }

    /**
     * Returns a variable, after it has been filtered.
     *
     * @param mixed $var
     *
     * @return string|ObjectProxy
     */
    protected function filterVar($var)
    {
        switch(gettype($var)) {
        case 'object':
            return self::factory($var, $this->savant);
        case 'string':
        case 'int':
        case 'double':
            return $this->savant->escape($var);
        case 'array':
            return new ObjectProxy\ArrayObject(
                new \ArrayObject($var),
                $this->savant
            );
        }
        return $var;
    }

    /**
     * Allows direct access to the entire object for situations where the proxy
     * interferes.
     *
     * @return mixed The raw object
     */
    function getRawObject()
    {
        return $this->object;
    }

    /**
     * Allows access to the raw member variables of the internal object.
     *
     * @return mixed
     */
    function getRaw($var)
    {
        return $this->object->$var;
    }

    function __set($var, $value)
    {
        $this->object->$var = $value;
    }

    /**
     * Magic method for checking if a property is set.
     *
     * @param string $var The var
     *
     * @return bool
     */
    function __isset($var)
    {
        return isset($this->object->$var);
    }

    /**
     * Unset a property.
     *
     * @param string $var The var
     *
     * @return void
     */
    function __unset($var)
    {
        unset($this->object->$var);
    }

    /**
     * Magic method which will call methods on the object.
     *
     * @return mixed
     */
    function __call($name, $arguments)
    {
        return $this->filterVar(
            call_user_func_array(
                array($this->object, $name),
                $arguments
            )
        );
    }

    /**
     * Gets the class of the internal object
     *
     * When using the ClassToTemplateMapper this method will be called to
     * determine the class of the object.
     *
     * @return string
     */
    function __getClass()
    {
        return get_class($this->object);
    }

    /**
     * Constructs an ObjectProxy for the given object.
     *
     * @param mixed $object The object to proxy
     * @param Main  $savant The main savant instance
     *
     * @return ObjectProxy
     */
    public static function factory($object, $savant)
    {
        if ($object instanceof \ArrayObject) {
            return new ObjectProxy\ArrayObject($object, $savant);
        }
        if ($object instanceof \Traversable) {
            return new ObjectProxy\Traversable($object, $savant);
        }
        if ($object instanceof \ArrayAccess) {
            return new ObjectProxy\ArrayAccess($object, $savant);
        }
        return new self($object, $savant);
    }

    function __toString()
    {
        if (method_exists($this->object, '__toString')) {
            return $this->savant->escape($this->object->__toString());
        }
        throw new BadMethodCallException(
            'Object of class ' . $this->__getClass()
            . ' could not be converted to string'
        );
    }

    /**
     * Returns the number of elements if the object has implemented Countable,
     * otherwise 1 is returned.
     *
     * @return int
     */
    function count()
    {
        return count($this->object);
    }
}
<?php
namespace PEAR2\Templates\Savant;

class TemplateException extends \Exception implements Exception {}
?><?php
namespace PEAR2\Templates\Savant;

class UnexpectedValueException extends \UnexpectedValueException implements Exception
{

}
<?php
namespace Pyrus\Developer\CoverageAnalyzer\Web {
use Pyrus\Developer\CoverageAnalyzer;
class Aggregator extends CoverageAnalyzer\Aggregator
{
    public $codepath;
    public $testpath;
    protected $sqlite;
    public $totallines = 0;
    public $totalcoveredlines = 0;

    /**
     * @var string $testpath Location of .phpt files
     * @var string $codepath Location of code whose coverage we are testing
     */
    function __construct($db = ':memory:')
    {
        $this->sqlite = new CoverageAnalyzer\Sqlite($db);
        $this->codepath = $this->sqlite->codepath;
        $this->testpath = $this->sqlite->testpath;
    }

    function retrieveLineLinks($file)
    {
        return $this->sqlite->retrieveLineLinks($file);
    }

    function retrievePaths()
    {
        return $this->sqlite->retrievePaths();
    }

    function retrievePathsForTest($test)
    {
        return $this->sqlite->retrievePathsForTest($test);
    }

    function retrieveTestPaths()
    {
        return $this->sqlite->retrieveTestPaths();
    }

    function coveragePercentage($sourcefile, $testfile = null)
    {
        return $this->sqlite->coveragePercentage($sourcefile, $testfile);
    }

    function coverageInfo($path)
    {
        return $this->sqlite->retrievePathCoverage($path);
    }

    function coverageInfoByTest($path, $test)
    {
        return $this->sqlite->retrievePathCoverageByTest($path, $test);
    }

    function retrieveCoverage($path)
    {
        return $this->sqlite->retrieveCoverage($path);
    }

    function retrieveProjectCoverage()
    {
        return $this->sqlite->retrieveProjectCoverage();
    }

    function retrieveCoverageByTest($path, $test)
    {
        return $this->sqlite->retrieveCoverageByTest($path, $test);
    }
}
}
?>
<?php
namespace Pyrus\Developer\CoverageAnalyzer\Web;
class ClassToTemplateMapper extends \PEAR2\Templates\Savant\ClassToTemplateMapper
{

    function map($class)
    {
        if ($class == 'Pyrus\Developer\CoverageAnalyzer\SourceFile\PerTest') {
            return 'SourceFile.tpl.php';
        }
        $class = str_replace(array('Pyrus\Developer\CoverageAnalyzer\Web', 'Pyrus\Developer\CoverageAnalyzer'), '', $class);
        return parent::map($class);
    }
}<?php
namespace Pyrus\Developer\CoverageAnalyzer\Web;
use Pyrus\Developer\CoverageAnalyzer\SourceFile;

class Controller {
    protected $view;
    protected $sqlite;
    public $actionable;
    public static $rooturl;
    public $options = array('view' => 'toc');

    function __construct($options = array())
    {
        $this->options    = $options + $this->options;
        $this->actionable = $this->route();
    }

    function route()
    {
        if (isset($this->options['restart'])) {
            unset($_SESSION['fullpath']);
            unset($this->options['setdatabase']);
        }

        if (!isset($this->options['setdatabase'])
            && !isset($_SESSION['fullpath'])) {
            return new SelectDatabase;
        }

        if (!isset($this->options['setdatabase'])) {
            $this->options['setdatabase'] = $_SESSION['fullpath'];
        }

        $_SESSION['fullpath'] = $this->options['setdatabase'];

        if (!file_exists($this->options['setdatabase'])) {
            return new SelectDatabase;
        }

        $this->sqlite = new Aggregator($this->options['setdatabase']);

        if (isset($this->options['file'])) {
            if (isset($this->options['test'])) {
                $source = new SourceFile\PerTest($this->options['file'], $this->sqlite, $this->sqlite->testpath, $this->sqlite->codepath, $this->options['test']);
            } else {
                $source = new SourceFile($this->options['file'], $this->sqlite, $this->sqlite->testpath, $this->sqlite->codepath);
            }

            if (isset($this->options['line'])) {
                return new LineSummary($source, $this->options['line'], $this->sqlite->testpath);
            }

            return $source;
        }

        if (isset($this->options['test'])) {
            if ($this->options['test'] === 'TOC') {
                return new TestSummary($this->sqlite);
            }
            return new TestCoverage($this->sqlite, $this->options['test']);
        }

        if (isset($this->options['file'])) {
            if (isset($this->options['line'])) {
                return $this->view->fileLineTOC($this->sqlite, $this->options['file'], $this->options['line']);
            }
            return $this->view->fileCoverage($this->sqlite, $this->options['file']);
        }

        return new Summary($this->sqlite);
    }

    function getRootLink()
    {
        return self::$rooturl;
    }

    function getFileLink($file, $test = null, $line = null)
    {
        if ($line) {
            return self::$rooturl . '?file=' . urlencode($file) . '&line=' . $line;
        }
        if ($test) {
            return self::$rooturl . '?file=' . urlencode($file) . '&test=' . $test;
        }
        return self::$rooturl . '?file=' . urlencode($file);
    }

    function getTOCLink($test = false)
    {
        if ($test === true) {
            return self::$rooturl . '?test=TOC';
        }
        if ($test) {
            return self::$rooturl . '?test=' . urlencode($test);
        }
        return self::$rooturl;
    }

    function getLogoutLink()
    {
        return $this->rooturl . '?restart=1';
    }

    function getDatabase()
    {
        $this->sqlite = $this->view->getDatabase();
    }
}

<?php
namespace Pyrus\Developer\CoverageAnalyzer\Web {
class Exception extends \Exception {}
}
?>
<?php
namespace Pyrus\Developer\CoverageAnalyzer\Web;
use Pyrus\Developer\CoverageAnalyzer\SourceFile;
class LineSummary extends \ArrayIterator
{
    public $source;
    public $line;

    function __construct($source, $line)
    {
        $this->source = $source;
        $this->line   = $line;
        parent::__construct($source->getLineLinks($this->line));
    }

    function __call($method, $args)
    {
        return $this->source->$method();
    }

    function __get($var)
    {
        return $this->source->$var;
    }
}<?php
namespace Pyrus\Developer\CoverageAnalyzer\Web;
use Pyrus\Developer\CoverageAnalyzer\SourceFile;
class SelectDatabase
{

}<?php
namespace Pyrus\Developer\CoverageAnalyzer\Web;
use Pyrus\Developer\CoverageAnalyzer\SourceFile;
class Summary extends \ArrayIterator
{
    public $sqlite;
    function __construct($sqlite)
    {
        $this->sqlite = $sqlite;
        parent::__construct($this->sqlite->retrievePaths());
    }

    function __call($method, $args)
    {
        return $this->sqlite->$method();
    }

    function __get($var)
    {
        return $this->sqlite->$var;
    }

    function current()
    {
        $current = parent::current();
        return new SourceFile($current, $this->sqlite, $this->sqlite->testpath, $this->sqlite->codepath, null, false);
    }
}<?php
namespace Pyrus\Developer\CoverageAnalyzer\Web;
use Pyrus\Developer\CoverageAnalyzer\SourceFile;
class TestCoverage extends \ArrayIterator
{
    public $sqlite;
    public $test;

    function __construct($sqlite, $test)
    {
        $this->sqlite = $sqlite;
        $this->test   = $test;
        parent::__construct($this->sqlite->retrievePathsForTest($test));
    }

    function __call($method, $args)
    {
        return $this->sqlite->$method();
    }

    function __get($var)
    {
        return $this->sqlite->$var;
    }

    function current()
    {
        $current = parent::current();
        return new SourceFile\PerTest($current, $this->sqlite, $this->sqlite->testpath, $this->sqlite->codepath, $this->test);
    }

}<?php
namespace Pyrus\Developer\CoverageAnalyzer\Web;
use Pyrus\Developer\CoverageAnalyzer\SourceFile;
class TestSummary extends \ArrayIterator
{
    public $sqlite;
    function __construct($sqlite)
    {
        $this->sqlite = $sqlite;
        parent::__construct($this->sqlite->retrieveTestPaths());
    }

    function __call($method, $args)
    {
        return $this->sqlite->$method();
    }

    function __get($var)
    {
        return $this->sqlite->$var;
    }

}<?php
namespace Pyrus\Developer\CoverageAnalyzer\Web;
use Pyrus\Developer\CoverageAnalyzer\SourceFile;
/**
 * Takes a source file and outputs HTML source highlighting showing the
 * number of hits on each line, highlights un-executed lines in red
 */
class View
{
    protected $savePath;
    protected $testPath;
    protected $sourcePath;
    protected $source;
    protected $controller;

    function getDatabase()
    {
        $output = new \XMLWriter;
        if (!$output->openUri('php://output')) {
            throw new Exception('Cannot open output - this should never happen');
        }
        $output->startElement('html');
         $output->startElement('head');
          $output->writeElement('title', 'Enter a path to the database');
         $output->endElement();
         $output->startElement('body');
          $output->writeElement('h2', 'Please enter the path to a coverage database');
          $output->startElement('form');
           $output->writeAttribute('name', 'getdatabase');
           $output->writeAttribute('method', 'GET');
           $output->writeAttribute('action', $this->controller->getTOCLink());
           $output->startElement('input');
            $output->writeAttribute('size', '90');
            $output->writeAttribute('type', 'text');
            $output->writeAttribute('name', 'setdatabase');
           $output->endElement();
           $output->startElement('input');
            $output->writeAttribute('type', 'submit');
           $output->endElement();
          $output->endElement();
         $output->endElement();
        $output->endElement();
        $output->endDocument();
    }

    function setController($controller)
    {
        $this->controller = $controller;
    }

    function logoutLink(\XMLWriter $output)
    {
        $output->startElement('h5');
         $output->startElement('a');
          $output->writeAttribute('href', $this->controller->getLogoutLink());
          $output->text('Current database: ' . $_SESSION['fullpath'] . '.  Click to start over');
         $output->endElement();
        $output->endElement();
    }

    function TOC($sqlite)
    {
        $coverage = $sqlite->retrieveProjectCoverage();
        $this->renderSummary($sqlite, $sqlite->retrievePaths(), false, $coverage[1], $coverage[0], $coverage[2]);
    }

    function testTOC($sqlite, $test = null)
    {
        if ($test) {
            return $this->renderTestCoverage($sqlite, $test);
        }
        $this->renderTestSummary($sqlite);
    }

    function fileLineTOC($sqlite, $file, $line)
    {
        $source = new SourceFile($file, $sqlite, $sqlite->testpath, $sqlite->codepath);
        return $this->renderLineSummary($file, $line, $sqlite->testpath, $source->getLineLinks($line));
    }

    function fileCoverage($sqlite, $file, $test = null)
    {
        if ($test) {
            $source = new SourceFile\PerTest($file, $sqlite, $sqlite->testpath, $sqlite->codepath, $test);
        } else {
            $source = new SourceFile($file, $sqlite, $sqlite->testpath, $sqlite->codepath);
        }
        return $this->render($source, $test);
    }

    function mangleFile($path, $istest = false)
    {
        return $this->controller->getFileLink($path, $istest);
    }

    function mangleTestFile($path)
    {
        return $this->controller->getTOClink($path);
    }

    function getLineLink($name, $line)
    {
        return $this->controller->getFileLink($name, null, $line);
    }

    function renderLineSummary($name, $line, $testpath, $tests)
    {
        $output = new \XMLWriter;
        if (!$output->openUri('php://output')) {
            throw new Exception('Cannot render ' . $name . ' line ' . $line . ', opening XML failed');
        }
        $output->setIndentString(' ');
        $output->setIndent(true);
        $output->startElement('html');
        $output->startElement('head');
        $output->writeElement('title', 'Tests covering line ' . $line . ' of ' . $name);
        $output->startElement('link');
        $output->writeAttribute('href', 'cover.css');
        $output->writeAttribute('rel', 'stylesheet');
        $output->writeAttribute('type', 'text/css');
        $output->endElement();
        $output->endElement();
        $output->startElement('body');
        $this->logoutLink($output);
        $output->writeElement('h2', 'Tests covering line ' . $line . ' of ' . $name);
        $output->startElement('p');
        $output->startElement('a');
        $output->writeAttribute('href', $this->controller->getTOCLink());
        $output->text('Aggregate Code Coverage for all tests');
        $output->endElement();
        $output->endElement();
        $output->startElement('p');
        $output->startElement('a');
        $output->writeAttribute('href', $this->mangleFile($name));
        $output->text('File ' . $name . ' code coverage');
        $output->endElement();
        $output->endElement();
        $output->startElement('ul');
        foreach ($tests as $testfile) {
            $output->startElement('li');
            $output->startElement('a');
            $output->writeAttribute('href', $this->mangleTestFile($testfile));
            $output->text(str_replace($testpath . '/', '', $testfile));
            $output->endElement();
            $output->endElement();
        }
        $output->endElement();
        $output->endElement();
        $output->endDocument();
    }

    /**
     * @param Pyrus\Developer\CodeCoverage\SourceFile $source
     * @param string $istest path to test file this is covering, or false for aggregate
     */
    function render(SourceFile $source, $istest = false)
    {
        $output = new \XMLWriter;
        if (!$output->openUri('php://output')) {
            throw new Exception('Cannot render ' . $source->shortName() . ', opening XML failed');
        }
        $output->setIndent(false);
        $output->startElement('html');
        $output->text("\n ");
        $output->startElement('head');
        $output->text("\n  ");
        if ($istest) {
            $output->writeElement('title', 'Code Coverage for ' . $source->shortName() . ' in ' .
                                  str_replace($source->testpath() . DIRECTORY_SEPARATOR, '', $istest));
        } else {
            $output->writeElement('title', 'Code Coverage for ' . $source->shortName());
        }
        $output->text("\n  ");
        $output->startElement('link');
        $output->writeAttribute('href', 'cover.css');
        $output->writeAttribute('rel', 'stylesheet');
        $output->writeAttribute('type', 'text/css');
        $output->endElement();
        $output->text("\n  ");
        $output->endElement();
        $output->text("\n ");
        $output->startElement('body');
        $output->text("\n ");
        $this->logoutLink($output);
        if ($istest) {
            $output->writeElement('h2', 'Code Coverage for ' . $source->shortName() . ' in ' .
                                  str_replace($source->testpath() . DIRECTORY_SEPARATOR, '', $istest));
        } else {
            $output->writeElement('h2', 'Code Coverage for ' . $source->shortName());
        }
        $output->text("\n ");
        $output->writeElement('h3', 'Coverage: ' . $source->coveragePercentage() . '% (Covered lines / Executable lines)');
        $info = $source->coverageInfo();
        $sourceCode = $source->source();

        $total = count($sourceCode);
        $output->writeRaw('<p><strong>' . $total . '</strong> total lines, of which <strong>' . $info[1] . '</strong> are executable, <strong>' . $info[2] .'</strong> are dead and <strong>' . ($total - $info[2] - $info[1]) . '</strong> are non-executable lines</p>');
        $output->writeRaw('<p>Of those <strong>' . $info[1] . '</strong> executable lines there are <strong>' . $info[0] . '</strong> lines covered with tests and <strong>' . ($info[1] - $info[0]) . '</strong> lack coverage</p>');
        $output->text("\n ");
        $output->startElement('p');
        $output->startElement('a');
        $output->writeAttribute('href', $this->controller->getTOCLink());
        $output->text('Aggregate Code Coverage for all tests');
        $output->endElement();
        $output->endElement();
        $output->startElement('pre');

        foreach ($sourceCode as $num => $line) {
            $coverage = $source->coverage($num);

            $output->startElement('span');
            $output->writeAttribute('class', 'ln');
            $output->text(str_pad($num, 8, ' ', STR_PAD_LEFT));
            $output->endElement();

            if ($coverage === false) {
                $output->text(str_pad(': ', 13, ' ', STR_PAD_LEFT) . $line);
                continue;
            }

            $output->startElement('span');
            $cov = is_array($coverage) ? $coverage['coverage'] : $coverage;
            if ($cov === -2) {
                $output->writeAttribute('class', 'dead');
                $output->text('           ');
            } elseif ($cov < 1) {
                $output->writeAttribute('class', 'nc');
                $output->text('           ');
            } else {
                $output->writeAttribute('class', 'cv');
                if (!$istest) {
                    $output->startElement('a');
                    $output->writeAttribute('href', $this->getLineLink($source->name(), $num));
                }

                $text = is_string($coverage) ? $coverage : $coverage['link'];
                $output->text(str_pad($text, 10, ' ', STR_PAD_LEFT) . ' ');
                if (!$istest) {
                    $output->endElement();
                }
            }

            $output->text(': ' .  $line);
            $output->endElement();
        }

        $output->endElement();
        $output->text("\n ");
        $output->endElement();
        $output->text("\n ");
        $output->endElement();
        $output->endDocument();
    }

    function renderSummary(Aggregator $agg, array $results, $istest = false, $total = 1, $covered = 1, $dead = 1)
    {
        $output = new \XMLWriter;
        if (!$output->openUri('php://output')) {
            throw new Exception('Cannot render test summary, opening XML failed');
        }
        $output->setIndentString(' ');
        $output->setIndent(true);
        $output->startElement('html');
        $output->startElement('head');
        if ($istest) {
            $output->writeElement('title', 'Code Coverage Summary [' . $istest . ']');
        } else {
            $output->writeElement('title', 'Code Coverage Summary');
        }
        $output->startElement('link');
        $output->writeAttribute('href', 'cover.css');
        $output->writeAttribute('rel', 'stylesheet');
        $output->writeAttribute('type', 'text/css');
        $output->endElement();
        $output->endElement();
        $output->startElement('body');
        if ($istest) {
            $output->writeElement('h2', 'Code Coverage Files for test ' . $istest);
        } else {
            $output->writeElement('h2', 'Code Coverage Files');
            $output->writeElement('h3', 'Total lines: ' . $total . ', covered lines: ' . $covered . ', dead lines: ' . $dead);
            $percent = 0;
            if ($total > 0) {
                $percent = round(($covered / $total) * 100, 1);
            }
            $output->startElement('p');
            if ($percent < 50) {
                $output->writeAttribute('class', 'bad');
            } elseif ($percent < 75) {
                $output->writeAttribute('class', 'ok');
            } else {
                $output->writeAttribute('class', 'good');
            }
            $output->text($percent . '% code coverage');
            $output->endElement();
        }
        $this->logoutLink($output);
        $output->startElement('p');
        $output->startElement('a');
        $output->writeAttribute('href', $this->controller->getTOCLink(true));
        $output->text('Code Coverage per PHPT test');
        $output->endElement();
        $output->endElement();
        $output->startElement('ul');
        foreach ($results as $i => $name) {
            $output->flush();
            $source = new SourceFile($name, $agg, $agg->testpath, $agg->codepath, null, false);
            $output->startElement('li');
            $percent = $source->coveragePercentage();
            $output->startElement('div');
            if ($percent < 50) {
                $output->writeAttribute('class', 'bad');
            } elseif ($percent < 75) {
                $output->writeAttribute('class', 'ok');
            } else {
                $output->writeAttribute('class', 'good');
            }
            $output->text(' Coverage: ' . str_pad($percent . '%', 4, ' ', STR_PAD_LEFT));
            $output->endElement();
            $output->startElement('a');
            $output->writeAttribute('href', $this->mangleFile($name, $istest));
            $output->text($source->shortName());
            $output->endElement();
            $output->endElement();
        }
        $output->endElement();
        $output->endElement();
        $output->endDocument();
    }

    function renderTestSummary(Aggregator $agg)
    {
        $output = new \XMLWriter;
        if (!$output->openUri('php://output')) {
                throw new Exception('Cannot render tests summary, opening XML failed');
        }
        $output->setIndentString(' ');
        $output->setIndent(true);
        $output->startElement('html');
        $output->startElement('head');
        $output->writeElement('title', 'Test Summary');
        $output->startElement('link');
        $output->writeAttribute('href', 'cover.css');
        $output->writeAttribute('rel', 'stylesheet');
        $output->writeAttribute('type', 'text/css');
        $output->endElement();
        $output->endElement();
        $output->startElement('body');
        $this->logoutLink($output);
        $output->writeElement('h2', 'Tests Executed, click for code coverage summary');
        $output->startElement('p');
        $output->startElement('a');
        $output->writeAttribute('href', $this->controller->getTOClink());
        $output->text('Aggregate Code Coverage for all tests');
        $output->endElement();
        $output->endElement();
        $output->startElement('ul');
        foreach ($agg->retrieveTestPaths() as $test) {
            $output->startElement('li');
            $output->startElement('a');
            $output->writeAttribute('href', $this->mangleTestFile($test));
            $output->text(str_replace($agg->testpath . '/', '', $test));
            $output->endElement();
            $output->endElement();
        }
        $output->endElement();
        $output->endElement();
        $output->endDocument();
    }

    function renderTestCoverage(Aggregator $agg, $test)
    {
        $reltest = str_replace($agg->testpath . '/', '', $test);
        $output = new \XMLWriter;
        if (!$output->openUri('php://output')) {
            throw new Exception('Cannot render test ' . $reltest . ' coverage, opening XML failed');
        }
        $output->setIndentString(' ');
        $output->setIndent(true);
        $output->startElement('html');
        $output->startElement('head');
        $output->writeElement('title', 'Code Coverage Summary for test ' . $reltest);
        $output->startElement('link');
        $output->writeAttribute('href', 'cover.css');
        $output->writeAttribute('rel', 'stylesheet');
        $output->writeAttribute('type', 'text/css');
        $output->endElement();
        $output->endElement();
        $output->startElement('body');
        $this->logoutLink($output);
        $output->writeElement('h2', 'Code Coverage Files for test ' . $reltest);
        $output->startElement('ul');
        $paths = $agg->retrievePathsForTest($test);
        foreach ($paths as $name) {
            $source = new SourceFile\PerTest($name, $agg, $agg->testpath, $agg->codepath, $test);
            $output->startElement('li');
            $percent = $source->coveragePercentage();
            $output->startElement('div');
            if ($percent < 50) {
                $output->writeAttribute('class', 'bad');
            } elseif ($percent < 75) {
                $output->writeAttribute('class', 'ok');
            } else {
                $output->writeAttribute('class', 'good');
            }
            $output->text(' Coverage: ' . str_pad($source->coveragePercentage() . '%', 4, ' ', STR_PAD_LEFT));
            $output->endElement();
            $output->startElement('a');
            $output->writeAttribute('href', $this->mangleFile($name, $test));
            $output->text($source->shortName());
            $output->endElement();
            $output->endElement();
        }
        $output->endElement();
        $output->endElement();
        $output->endDocument();
    }
}
<?php
namespace Pyrus\Developer\CoverageAnalyzer;
class SourceFile
{
    protected $source;
    protected $path;
    protected $sourcepath;
    protected $coverage;
    protected $aggregator;
    protected $testpath;
    protected $linelinks;

    function __construct($path, Aggregator $agg, $testpath, $sourcepath, $coverage = true)
    {
        $this->source = file($path);
        $this->path = $path;
        $this->sourcepath = $sourcepath;

        array_unshift($this->source, '');
        unset($this->source[0]); // make source array indexed by line number

        $this->aggregator = $agg;
        $this->testpath = $testpath;
        if ($coverage === true) {
            $this->setCoverage();
        }
    }

    function setCoverage()
    {
        $this->coverage = $this->aggregator->retrieveCoverage($this->path);
    }

    function aggregator()
    {
        return $this->aggregator;
    }

    function testpath()
    {
        return $this->testpath;
    }

    function render(AbstractSourceDecorator $decorator = null)
    {
        if ($decorator === null) {
            $decorator = new DefaultSourceDecorator('.');
        }
        return $decorator->render($this);
    }

    function coverage($line = null)
    {
        if ($line === null) {
            return $this->coverage;
        }

        if (!isset($this->coverage[$line])) {
            return false;
        }

        return $this->coverage[$line];
    }

    function coveragePercentage()
    {
        return $this->aggregator->coveragePercentage($this->path);
    }

    /**
     * Get all the coverage info for this file
     *
     * @return array(covered, total, dead)
     */
    function coverageInfo()
    {
        return $this->aggregator->coverageInfo($this->path);
    }

    function name()
    {
        return $this->path;
    }

    function shortName()
    {
        return str_replace($this->sourcepath . DIRECTORY_SEPARATOR, '', $this->path);
    }

    function source()
    {
        $cov = $this->coverage();
        if (empty($cov)) {
            return $this->source;
        }

        /* Make sure we have as many lines as required
         * Sometimes Xdebug returns coverage on one line beyond what
         * our file has, this is PHP doing a return on the file.
         */
        $endLine = max(array_keys($cov));
        if (count($this->source) < $endLine) {
            // Add extra new line if required since we use <pre> to format
            $secondLast = $endLine - 1;
            $this->source[$secondLast] = str_replace("\r", '', $this->source[$secondLast]);
            $len = strlen($this->source[$secondLast]) - 1;
            if (substr($this->source[$secondLast], $len) != "\n") {
                $this->source[$secondLast] .= "\n";
            }

            $this->source[$endLine] = "\n";
        }

        return $this->source;
    }

    function coveredLines()
    {
        $info = $this->aggregator->coverageInfo($this->path);
        return $info[0];
    }

    function getLineLinks($line)
    {
        if (!isset($this->linelinks)) {
            $this->linelinks = $this->aggregator->retrieveLineLinks($this->path);
        }

        if (isset($this->linelinks[$line])) {
            return $this->linelinks[$line];
        }

        return false;
    }
}<?php
namespace Pyrus\Developer\CoverageAnalyzer {
class Aggregator
{
    protected $codepath;
    protected $testpath;
    protected $sqlite;
    public $totallines = 0;
    public $totalcoveredlines = 0;

    /**
     * @var string $testpath Location of .phpt files
     * @var string $codepath Location of code whose coverage we are testing
     */
    function __construct($testpath, $codepath, $db = ':memory:')
    {
        $newcodepath = realpath($codepath);
        if (!$newcodepath) {
            if (!strpos($codepath, '://') || !file_exists($codepath)) {
                // stream wrapper not found
                throw new Exception('Can not find code path ' . $codepath);
            }
        } else {
            $codepath = $newcodepath;
        }

        $files = array();
        foreach (new \RegexIterator(
                    new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($codepath, 0|\RecursiveDirectoryIterator::SKIP_DOTS)
                    ),
                    '/\.php$/') as $file) {
            if (strpos((string) $file, '.svn') || strpos($testpath, (string)$file)) {
                continue;
            }

            $files[] = realpath((string) $file);
        }

        $this->sqlite = new Sqlite($db, $codepath, $testpath, $files);
        $this->codepath = $codepath;
        $this->sqlite->begin();

        echo "Scanning for xdebug coverage files...\n";
        $files = $this->scan($testpath);
        echo "done\n";

        echo "Parsing xdebug results\n";
        if (!count($files)) {
            echo "done (no modified xdebug files)\n";
            return;
        }

        $delete = array();
        foreach ($files as $testid => $xdebugfile) {
            $phpt = str_replace('.xdebug', '.phpt', $xdebugfile);
            if (!file_exists($phpt)) {
                $delete[] = $xdebugfile;
                continue;
            }

            $id = $this->sqlite->addTest($phpt);
            echo '(' . $testid . ' of ' . count($files) . ') ' . $xdebugfile;
            $this->retrieveXdebug($xdebugfile, $id);
            echo "\ndone\n";
        }

        $this->sqlite->addNoCoverageFiles();
        $this->sqlite->updateAllLines();
        $this->sqlite->updateTotalCoverage();
        $this->sqlite->commit();

        if (count($delete)) {
            echo "\nNote: The following .xdebug files were outdated relics and have been deleted\n";
            foreach ($delete as $d) {
                unlink($d);
                echo "$d\n";
            }
            echo "\n";
        }
    }

    function retrieveLineLinks($file)
    {
        return $this->sqlite->retrieveLineLinks($file);
    }

    function retrievePaths()
    {
        return $this->sqlite->retrievePaths();
    }

    function retrievePathsForTest($test)
    {
        return $this->sqlite->retrievePathsForTest($test);
    }

    function retrieveTestPaths()
    {
        return $this->sqlite->retrieveTestPaths();
    }

    function coveragePercentage($sourcefile, $testfile = null)
    {
        return $this->sqlite->coveragePercentage($sourcefile, $testfile);
    }

    function coverageInfo($path)
    {
        return $this->sqlite->retrievePathCoverage($path);
    }

    function coverageInfoByTest($path, $test)
    {
        return $this->sqlite->retrievePathCoverageByTest($path, $test);
    }

    function retrieveCoverage($path)
    {
        return $this->sqlite->retrieveCoverage($path);
    }

    function retrieveCoverageByTest($path, $test)
    {
        return $this->sqlite->retrieveCoverageByTest($path, $test);
    }

    function retrieveProjectCoverage()
    {
        return $this->sqlite->retrieveProjectCoverage();
    }

    function retrieveXdebug($path, $testid)
    {
        if (file_exists($path) === false) {
            return;
        }

        $source = '$xdebug = ' . file_get_contents($path) . ";\n";
        eval($source);
        $this->sqlite->addCoverage(str_replace('.xdebug', '.phpt', $path), $testid, $xdebug);
    }

    function scan($path)
    {
        $testpath = realpath($path);
        if (!$testpath) {
            throw new Exception('Unable to process path' . $path);
        }

        $this->testpath = str_replace('\\', '/', $testpath);

        // get a list of all xdebug files
        $xdebugs = array();
        foreach (new \RegexIterator(
                    new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($this->testpath,
                                                        0|\RecursiveDirectoryIterator::SKIP_DOTS)
                    ), '/\.xdebug$/') as $file
        ) {
            if (strpos((string) $file, '.svn')) {
                continue;
            }

            $xdebugs[] = realpath((string) $file);
        }
        echo count($xdebugs), " total...\n";

        $unmodified = $modified = array();
        foreach ($xdebugs as $path) {
            if ($this->sqlite->unChangedXdebug($path)) {
                $unmodified[$path] = true;
                continue;
            }

            $modified[] = $path;
        }

        $xdebugs = $modified;
        sort($xdebugs);
        // index from 1
        array_unshift($xdebugs, '');
        unset($xdebugs[0]);
        $test = array_flip($xdebugs);

        echo "\n\n";
        foreach ($this->sqlite->retrieveTestPaths() as $path) {
            $xdebugpath = str_replace('.phpt', '.xdebug', $path);
            if (isset($test[$xdebugpath]) || isset($unmodified[$xdebugpath])) {
                continue;
            }

            // remove outdated tests
            echo "Removing results from $xdebugpath\n";
            $this->sqlite->removeOldTest($path);
        }

        return $xdebugs;
    }

    function render($toPath)
    {
        $decorator = new DefaultSourceDecorator($toPath, $this->testpath, $this->codepath);
        echo "Generating project coverage data...\n";
        $coverage = $this->sqlite->retrieveProjectCoverage();
        echo "done\n";
        $decorator->renderSummary($this, $this->retrievePaths(), $this->codepath, false, $coverage[1],
                                  $coverage[0], $coverage[2]);
        $a = $this->codepath;
        echo "[Step 2 of 2] Rendering per-test coverage...\n";
        $decorator->renderTestCoverage($this, $this->testpath, $a);
        echo "done\n";
    }
}
}
?>
<?php
namespace Pyrus\Developer\CoverageAnalyzer {
class Exception extends \Exception {}
}
?>
<?php
namespace Pyrus\Developer\CoverageAnalyzer;
class Sqlite
{
    public $codepath;
    public $testpath;

    protected $db;
    protected $totallines   = 0;
    protected $coveredlines = 0;
    protected $deadlines    = 0;
    protected $pathCovered  = array();
    protected $pathTotal    = array();
    protected $pathDead     = array();

    private $statement;
    private $lines   = array();
    private $files   = array();
    private $deleted = array();

    const COVERAGE_COVERED      = 1;
    const COVERAGE_NOT_EXECUTED = 0;
    const COVERAGE_NOT_COVERED  = -1;
    const COVERAGE_DEAD         = -2;

    function __construct($path = ':memory:', $codepath = null, $testpath = null, $codefiles = array())
    {
        $this->files = $codefiles;
        $this->db = new \Sqlite3($path);
        $this->db->exec('PRAGMA temp_store=2');
        $this->db->exec('PRAGMA count_changes=OFF');

        $version = '5.3.0';
        $sql = 'SELECT version FROM analyzerversion';
        if (@$this->db->querySingle($sql) == $version) {
            $this->codepath = $this->db->querySingle('SELECT codepath FROM paths');
            $this->testpath = $this->db->querySingle('SELECT testpath FROM paths');
            return;
        }

        // restart the database
        echo "Upgrading database to version $version";
        if (!$codepath || !$testpath) {
            throw new Exception('Both codepath and testpath must be set in ' .
                                'order to initialize a coverage database');
        }

        $this->codepath = $codepath;
        $this->testpath = $testpath;
        $this->db->exec('DROP TABLE IF EXISTS coverage;');
        echo ".";
        $this->db->exec('DROP TABLE IF EXISTS coverage_nonsource;');
        echo ".";
        $this->db->exec('DROP TABLE IF EXISTS not_covered;');
        echo ".";
        $this->db->exec('DROP TABLE IF EXISTS files;');
        echo ".";
        $this->db->exec('DROP TABLE IF EXISTS tests;');
        echo ".";
        $this->db->exec('DROP TABLE IF EXISTS paths;');
        echo ".";
        $this->db->exec('DROP TABLE IF EXISTS coverage_per_file;');
        echo ".";
        $this->db->exec('DROP TABLE IF EXISTS line_info;');
        echo ".";
        $this->db->exec('DROP TABLE IF EXISTS all_lines;');
        echo ".";
        $this->db->exec('DROP TABLE IF EXISTS xdebugs;');
        echo ".";
        $this->db->exec('DROP TABLE IF EXISTS analyzerversion;');

        echo ".";
        $this->db->exec('BEGIN');

        $sql = '
            CREATE TABLE coverage (
              files_id integer NOT NULL,
              tests_id integer NOT NULL,
              linenumber INTEGER NOT NULL,
              state INTEGER NOT NULL,
              PRIMARY KEY (files_id, linenumber, tests_id)
            );

            CREATE INDEX idx_coveragestats  ON coverage (files_id, tests_id, state);
            CREATE INDEX idx_coveragestats2 ON coverage (files_id, linenumber, tests_id, state);
            CREATE INDEX idx_coveragestats3 ON coverage (files_id, tests_id);

            CREATE TABLE all_lines (
              files_id integer NOT NULL,
              linenumber INTEGER NOT NULL,
              state INTEGER NOT NULL,
              PRIMARY KEY (files_id, linenumber, state)
            );

             CREATE INDEX idx_all_lines_stats ON all_lines (files_id, linenumber);

            CREATE TABLE line_info (
              files_id integer NOT NULL,
              covered INTEGER NOT NULL,
              dead  INTEGER NOT NULL,
              total INTEGER NOT NULL,
              PRIMARY KEY (files_id)
            );
          ';
        $this->exec($sql);

        echo ".";
        $sql = '
          CREATE TABLE coverage_nonsource (
            files_id integer NOT NULL,
            tests_id integer NOT NULL,
            PRIMARY KEY (files_id, tests_id)
          );
          ';
        $this->exec($sql);

        echo ".";
        $sql = '
          CREATE TABLE files (
            id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            path TEXT(500) NOT NULL,
            hash TEXT(32) NOT NULL,
            issource BOOL NOT NULL,
            UNIQUE (path)
          );
          CREATE INDEX files_issource on files (issource);
          ';
        $this->exec($sql);

        echo ".";
        $sql = '
          CREATE TABLE xdebugs (
            path TEXT(500) NOT NULL,
            hash TEXT(32) NOT NULL,
            PRIMARY KEY (path)
          );';
        $this->exec($sql);

        echo ".";
        $sql = '
          CREATE TABLE tests (
            id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            testpath TEXT(500) NOT NULL,
            hash TEXT(32) NOT NULL,
            UNIQUE (testpath)
          );';
        $this->exec($sql);

        echo ".";
        $sql = '
          CREATE TABLE analyzerversion (
            version TEXT(5) NOT NULL
          );

          INSERT INTO analyzerversion VALUES("' . $version . '");

          CREATE TABLE paths (
            codepath TEXT NOT NULL,
            testpath TEXT NOT NULL
          );';
        $this->exec($sql);

        echo ".";
        $sql = '
          INSERT INTO paths VALUES(
            "' . $this->db->escapeString($codepath) . '",
            "' . $this->db->escapeString($testpath). '");';
        $this->exec($sql);
        $this->db->exec('COMMIT');
        echo "done\n";
    }

    public function exec($sql)
    {
        $worked = $this->db->exec($sql);
        if (!$worked) {
            @$this->db->exec('ROLLBACK');
            $error = $this->db->lastErrorMsg();
            throw new Exception('Unable to create Code Coverage SQLite3 database: ' . $error);
        }
    }

    function retrieveLineLinks($file, $id = null)
    {
        if ($id === null) {
            $id = $this->getFileId($file);
        }

        $sql = 'SELECT t.testpath, c.linenumber
            FROM
                coverage c, tests t
            WHERE
                c.files_id = ' . $id . ' AND t.id = c.tests_id';
        $result = $this->db->query($sql);
        if (!$result) {
            $error = $this->db->lastErrorMsg();
            throw new Exception('Cannot retrieve line links for ' . $file .
                                ' line #' . $line .  ': ' . $error);
        }

        $ret = array();
        while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
            $ret[$res['linenumber']][] = $res['testpath'];
        }
        return $ret;
    }

    function retrieveTestPaths()
    {
        $sql = 'SELECT testpath from tests ORDER BY testpath';
        $result = $this->db->query($sql);
        if (!$result) {
            $error = $this->db->lastErrorMsg();
            throw new Exception('Cannot retrieve test paths :' . $error);
        }
        $ret = array();
        while ($res = $result->fetchArray(SQLITE3_NUM)) {
            $ret[] = $res[0];
        }
        return $ret;
    }

    function retrievePathsForTest($test, $all = 0)
    {
        $id = $this->getTestId($test);
        $ret = array();
        if ($all) {
            $sql = '
                SELECT DISTINCT path
                FROM coverage_nonsource c, files
                WHERE c.tests_id = ' . $id . '
                    AND files.id = c.files_id
                GROUP BY c.files_id
                ORDER BY path';
            $result = $this->db->query($sql);
            if (!$result) {
                $error = $this->db->lastErrorMsg();
                throw new Exception('Cannot retrieve file paths for test ' . $test . ':' . $error);
            }

            while ($res = $result->fetchArray(SQLITE3_NUM)) {
                $ret[] = $res[0];
            }
        }

        $sql = '
            SELECT DISTINCT path
            FROM coverage c, files
            WHERE
                c.tests_id = ' . $id . '
              AND
                files.id = c.files_id
            GROUP BY c.files_id
            ORDER BY path';
        $result = $this->db->query($sql);
        if (!$result) {
            $error = $this->db->lastErrorMsg();
            throw new Exception('Cannot retrieve file paths for test ' . $test . ':' . $error);
        }

        while ($res = $result->fetchArray(SQLITE3_NUM)) {
            $ret[] = $res[0];
        }

        return $ret;
    }

    function retrievePaths($all = 0)
    {
        if ($all) {
            $sql = 'SELECT path from files ORDER BY path';
        } else {
            $sql = 'SELECT path from files WHERE issource = 1 ORDER BY path';
        }

        $result = $this->db->query($sql);
        if (!$result) {
            $error = $this->db->lastErrorMsg();
            throw new Exception('Cannot retrieve file paths :' . $error);
        }

        $ret = array();
        while ($res = $result->fetchArray(SQLITE3_NUM)) {
            $ret[] = $res[0];
        }

        return $ret;
    }

    function coveragePercentage($sourcefile, $testfile = null)
    {
        if ($testfile) {
            $coverage = $this->retrievePathCoverageByTest($sourcefile, $testfile);
        } else {
            $coverage = $this->retrievePathCoverage($sourcefile);
        }

        if ($coverage[1]) {
            return round(($coverage[0] / $coverage[1]) * 100, 1);
        }

        return 0;
    }

    function retrieveProjectCoverage($path = null)
    {
        if ($this->totallines) {
            return array($this->coveredlines, $this->totallines, $this->deadlines);
        }

        $sql = '
            SELECT covered, total, dead, path
            FROM line_info, files
            WHERE files.id = line_info.files_id';
        if ($path !== null) {
            $sql .= ' AND files.path = "' . $this->db->escapeString($path) . '"';
        }

        $result = $this->db->query($sql);
        if (!$result) {
            $error = $this->db->lastErrorMsg();
            throw new Exception('Cannot retrieve coverage for ' . $path.  ': ' . $error);
        }

        while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
            $this->pathTotal[$res['path']]   = $res['total'];
            $this->pathCovered[$res['path']] = $res['covered'];
            $this->pathDead[$res['path']]    = $res['dead'];
            $this->coveredlines += $res['covered'];
            $this->totallines   += $res['total'];
            $this->deadlines    += $res['dead'];
        }

        return array($this->coveredlines, $this->totallines, $this->deadlines);
    }

    function retrievePathCoverage($path)
    {
        if (!$this->totallines) {
            // set up the cache
            $this->retrieveProjectCoverage($path);
        }

        if (!isset($this->pathCovered[$path])) {
            return array(0, 0, 0);
        }

        return array($this->pathCovered[$path], $this->pathTotal[$path], $this->pathDead[$path]);
    }

    function retrievePathCoverageByTest($path, $test)
    {
        $id = $this->getFileId($path);
        $testid = $this->getTestId($test);

        $sql = '
            SELECT state, COUNT(linenumber) AS ln
            FROM coverage
            WHERE files_id = ' . $id. ' AND tests_id = ' . $testid . '
            GROUP BY state';
        $result = $this->db->query($sql);
        if (!$result) {
            $error = $this->db->lastErrorMsg();
            throw new Exception('Cannot retrieve path coverage for ' . $path .
                                ' in test ' . $test . ': ' . $error);
        }

        $total = $dead = $covered = 0;
        while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
            if ($res['state'] === Sqlite::COVERAGE_COVERED) {
                $covered = $res['ln'];
            }

            if ($res['state'] === Sqlite::COVERAGE_DEAD) {
                $dead = $res['ln'];
            }

            $total += $res['ln'];
        }

        return array($covered, $total, $dead);
    }

    function retrieveCoverageByTest($path, $test)
    {
        $id = $this->getFileId($path);
        $testid = $this->getTestId($test);

        $sql = 'SELECT state AS coverage, linenumber FROM coverage
                    WHERE files_id = ' . $id . ' AND tests_id = ' . $testid . '
                    ORDER BY linenumber ASC';
        $result = $this->db->query($sql);
        if (!$result) {
            $error = $this->db->lastErrorMsg();
            throw new Exception('Cannot retrieve test ' . $test .
                                ' coverage for ' . $path.  ': ' . $error);
        }

        $ret = array();
        while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
            $ret[$res['linenumber']] = $res['coverage'];
        }

        return $ret;
    }

    function getFileId($path)
    {
        $sql = 'SELECT id FROM files WHERE path = "' . $this->db->escapeString($path) .'"';
        $id = $this->db->querySingle($sql);
        if ($id === false || $id === null) {
            throw new Exception('Unable to retrieve file ' . $path . ' id from database');
        }

        return $id;
    }

    function getTestId($path)
    {
        $sql = 'SELECT id FROM tests WHERE testpath = "' . $this->db->escapeString($path) . '"';
        $id = $this->db->querySingle($sql);
        if ($id === false || $id === null) {
            throw new Exception('Unable to retrieve test file ' . $path . ' id from database');
        }

        return $id;
    }

    function removeOldTest($testpath, $id = null)
    {
        if ($id === null) {
            $id = $this->getTestId($testpath);
        }

        // gather information
        $sql = 'SELECT DISTINCT files_id FROM coverage
                WHERE
                    tests_id = ' . $id ;
        if (!empty($this->deleted)) {
            $sql .= '
                AND
                    files_id NOT IN (' . implode(', ', $this->deleted) . ')';
        }

        $result = $this->db->query($sql);
        while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
            $this->deleted[] = $res['files_id'];
        }

        echo "\ndeleting old test ", $testpath," .";
        $this->db->exec('DELETE FROM tests WHERE id = ' . $id);
        echo '.';
        $this->db->exec('DELETE FROM coverage WHERE tests_id = ' . $id);
        echo '.';
        $this->db->exec('DELETE FROM coverage_nonsource WHERE tests_id = ' . $id);
        echo '.';
        $p = $this->db->escapeString(str_replace('.phpt', '.xdebug', $testpath));
        $this->db->exec('DELETE FROM xdebugs WHERE path = "' . $p . '"');
        echo " done\n";
    }

    function addTest($testpath, $id = null)
    {
        try {
            $id = $this->getTestId($testpath);
            $this->db->exec('UPDATE tests SET hash = "' . md5_file($testpath) . '" WHERE id = ' . $id);
        } catch (Exception $e) {
            echo "Adding new test $testpath\n";
            $sql = 'INSERT INTO tests (testpath, hash) VALUES(:testpath, :md5)';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':testpath', $testpath);
            $stmt->bindValue(':md5', md5_file($testpath));
            $stmt->execute();
            $id = $this->db->lastInsertRowID();
        }

        $file  = str_replace('.phpt', '.xdebug', $testpath);
        $sql = 'REPLACE INTO xdebugs (path, hash) VALUES(:path, :md5)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':path', $file);
        $stmt->bindValue(':md5', md5_file($file));
        $stmt->execute();

        return $id;
    }

    function unChangedXdebug($path)
    {
        $sql = 'SELECT hash FROM xdebugs WHERE path = "' . $this->db->escapeString($path) . '"';
        $md5 = $this->db->querySingle($sql);
        if (!$md5 || $md5 != md5_file($path)) {
            return false;
        }

        return true;
    }

    function retrieveCoverage($path)
    {
        $id = $this->getFileId($path);
        $links = $this->retrieveLineLinks($path, $id);
        $links = array_map(function ($arr) {return count($arr);}, $links);

        $sql = '
            SELECT state AS coverage, linenumber
            FROM all_lines
            WHERE files_id = ' . $id . '
            ORDER BY linenumber ASC';
        $result = $this->db->query($sql);
        if (!$result) {
            $error = $this->db->lastErrorMsg();
            throw new Exception('Cannot retrieve coverage for ' . $path.  ': ' . $error);
        }

        $return = array();
        while ($res = $result->fetchArray()) {
            if (!isset($return[$res['linenumber']])) {
                $return[$res['linenumber']] = array();
            }

            if (
                !isset($return[$res['linenumber']]['coverage']) ||
                $return[$res['linenumber']]['coverage'] !== Sqlite::COVERAGE_COVERED
            ) {
                // Found a case where a line could be dead and not covered, we still don't know why
                if (
                    isset($return[$res['linenumber']]['coverage']) &&
                    $return[$res['linenumber']]['coverage'] === Sqlite::COVERAGE_NOT_COVERED &&
                    $res['coverage'] === Sqlite::COVERAGE_DEAD
                ) {
                    continue;
                }

                $return[$res['linenumber']]['coverage'] = $res['coverage'];
            }


            if (isset($links[$res['linenumber']])) {
                $return[$res['linenumber']]['link'] = $links[$res['linenumber']];
            } else {
                $return[$res['linenumber']]['link'] = 0;
            }
        }

        return $return;
    }

    function updateTotalCoverage()
    {
        echo "Updating coverage per-file intermediate table\n";

        $sql = '
            SELECT files_id, linenumber, state
            FROM all_lines
            ORDER BY files_id, linenumber ASC';
        $result = $this->db->query($sql);
        $lines = array();
        while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
            if (!isset($lines[$res['files_id']])) {
                $lines[$res['files_id']] = array();
            }

            $lines[$res['files_id']][$res['linenumber']] = $res['state'];
        }

        $ret = array();
        foreach ($lines as $file => $lines) {
            $ret[$file]['covered']     = 0;
            $ret[$file]['dead']        = 0;
            $ret[$file]['not_covered'] = 0;
            foreach (array_count_values($lines) as $state => $count) {
                if ($state === Sqlite::COVERAGE_COVERED) {
                    $ret[$file]['covered'] = $count;
                }

                if ($state === Sqlite::COVERAGE_NOT_COVERED) {
                    $ret[$file]['not_covered'] = $count;
                }

                if ($state === Sqlite::COVERAGE_DEAD) {
                    $ret[$file]['dead'] = $count;
                }
            }
        }

        foreach ($ret as $id => $line) {
            $covered     = $line['covered'];
            $dead        = $line['dead'];
            $not_covered = $line['not_covered'];
            $this->db->exec('REPLACE INTO line_info (files_id, covered, dead, total)
                            VALUES(' . $id . ',' . $covered . ',' . $dead . ',' . ($covered + $not_covered) . ')');
            echo ".";
        }

        echo "\ndone\n";
    }

    public function updateAllLines()
    {
        echo "Updating the all lines internal table\n";

        $keys = implode(', ', array_keys($this->lines));
        $sql = '
            SELECT files_id, linenumber, state
            FROM all_lines
            WHERE files_id IN (' . $keys . ')
            ORDER BY linenumber ASC';

        $result = $this->db->query($sql);
        $data = array();
        while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
            if (!isset($data[$res['files_id']])) {
                $data[$res['files_id']] = array();
            }

            $data[$res['files_id']][$res['linenumber']] = $res['state'];
        }

        foreach ($data as $id => $lines) {
            foreach ($lines as $line => $state) {
                if (
                    // Only allow lines that are in the new rollout.
                    isset($this->lines[$id][$line]) ||
                    // Line already marked as covered.
                    (
                        isset($this->lines[$id][$line]) &&
                        (
                         $this->lines[$id][$line] !== Sqlite::COVERAGE_COVERED ||
                         $state > $this->lines[$id][$line]
                        )
                    )
                ) {
                    $this->lines[$id][$line] = $state;
                }
            }
        }
        unset($data);

        echo '.';
        $sql  = 'DELETE FROM all_lines WHERE files_id IN (' . $keys . ');';
        $this->db->exec($sql);

        $sql = 'INSERT INTO all_lines (files_id, linenumber, state) VALUES (:id, :line, :state);';
        $stmt = $this->db->prepare($sql);
        foreach ($this->lines as $file => $lines) {
            if (!is_array($lines)) {
                continue;
            }

            echo '.';
            foreach ($lines as $line => $state) {
                $stmt->bindValue(':id',    $file,  SQLITE3_INTEGER);
                $stmt->bindValue(':line',  $line,  SQLITE3_INTEGER);
                $stmt->bindValue(':state', $state, SQLITE3_INTEGER);
                $stmt->execute();
            }
        }

        echo "\ndone\n";
    }

    function addFile($path, $issource = 0)
    {
        $sql = 'SELECT id FROM files WHERE path = "' . $this->db->escapeString($path) . '"';
        $id = $this->db->querySingle($sql);
        if ($id === false) {
            throw new Exception('Unable to add file ' . $path . ' to database');
        }

        if ($id !== null) {
            $sql = 'UPDATE files SET hash = :md5, issource = :issource WHERE path = :path';
        } else {
            $sql = 'INSERT INTO files (path, hash, issource) VALUES(:path, :md5, :issource)';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':path',     $path);
        $stmt->bindValue(':md5',      md5_file($path));
        $stmt->bindValue(':issource', $issource);
        if (!$stmt->execute()) {
            throw new Exception('Problem running this particular SQL: ' . $sql);
        }

        if ($id === null) {
            $id = $this->db->lastInsertRowID();
        }

        return $id;
    }

    public function addNoCoverageFiles()
    {
        echo "Adding files with no coverage information\n";

        // Start by pruning out files we already have information about
        $sql = 'SELECT * FROM files WHERE issource = 1';
        $result = $this->db->query($sql);
        while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
            $key = array_search($res['path'], $this->files);
            if (isset($this->files[$key])) {
                unset($this->files[$key]);
            }
        }

        $codepath = $this->codepath;
        spl_autoload_register(function($class) use ($codepath){
            $file = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class);
            if (file_exists($codepath . DIRECTORY_SEPARATOR . $file . '.php')) {
                include $codepath . DIRECTORY_SEPARATOR . $file . '.php';
                return true;
            }
            if ($file = stream_resolve_include_path($file . '.php')) {
                include $file;
                return true;
            }

            if (!class_exists($class)) {
                $fake_class = '<?php class '.$class.' {}';
                $fake_class_file = tempnam(sys_get_temp_dir(), 'pyrus_tmp');
                file_put_contents($fake_class_file, $fake_class);
                include $fake_class_file;
                unlink($fake_class_file);
            }

            return true;
        });

        foreach ($this->files as $file) {
            if (empty($file)) {
                continue;
            }

            echo "$file\n";
            $id = $this->addFile($file, 1);

            // Figure out of the file has been already inclduded or not
            $included = false;

            $relative_file = substr($file, strlen($this->codepath . DIRECTORY_SEPARATOR), -4);

            // We need to try a few things here to actually find the correct class
            // Foo/Bar.php may mean Foo_Bar Foo\Bar or PEAR2\Foo\Bar
            $class       = str_replace('/', '_', $relative_file);
            $ns_class    = str_replace('/', '\\', $relative_file);
            $pear2_class = 'PEAR2\\' . $ns_class;

            $classes = array_merge(get_declared_classes(), get_declared_interfaces());

            if (in_array($class, $classes)
                || in_array($ns_class, $classes)
                || in_array($pear2_class, $classes)) {
                $included = true;
            }

            // Get basic coverage information on the file
            if ($included === false) {
                xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
                include $file;
                $data = xdebug_get_code_coverage(true);
                $this->lines[$id] = $data[$file];
            } else {
                /*
                 * @TODO files that already have been loaded need to have
                 * their missing coverage lines added too
                 */
            }
        }

        echo "Done\n";
    }

    function addCoverage($testpath, $testid, $xdebug)
    {
        $sql = 'DELETE FROM coverage WHERE tests_id = ' . $testid . ';
                DELETE FROM coverage_nonsource WHERE tests_id = ' . $testid;
        $worked = $this->db->exec($sql);

        echo "\n";
        foreach ($xdebug as $path => $results) {
            if (!file_exists($path)) {
                continue;
            }

            $issource = 1;
            if (
                strpos($path, $this->codepath) !== 0 ||
                strpos($path, $this->testpath) === 0
            ) {
                $issource = 0;
            }

            echo ".";
            $id = $this->addFile($path, $issource);
            $key = array_search($path, $this->files);
            if (isset($this->files[$key])) {
                unset($this->files[$key]);
            }

            if ($issource) {
                if (!isset($this->lines[$id])) {
                    $this->lines[$id] = array();
                }
            } elseif (!$issource) {
                $sql2 = 'INSERT INTO coverage_nonsource
                        (files_id, tests_id)
                        VALUES(' . $id . ', ' . $testid . ')';
                $worked = $this->db->exec($sql2);
                if (!$worked) {
                    $error = $this->db->lastErrorMsg();
                    throw new Exception('Cannot add coverage for test ' . $testpath .
                                        ', covered file ' . $path . ': ' . $error);
                }
                continue;
            }

            $sql = '';
            foreach ($results as $line => $state) {
                if (!$line) {
                    continue; // line 0 does not exist, skip this (xdebug quirk)
                }

                if ($issource) {
                    if (
                        !isset($this->lines[$id][$line]) ||
                        // Line already marked as covered.
                        $this->lines[$id][$line] !== Sqlite::COVERAGE_COVERED ||
                        $state > $this->lines[$id][$line]
                    ) {
                        $this->lines[$id][$line] = $state;
                    }
                }

                $sql .= 'INSERT INTO coverage
                    (files_id, tests_id, linenumber, state)
                    VALUES (' . $id . ', ' . $testid . ', ' . $line . ', ' . $state. ');';
            }

            if ($sql !== '') {
                $worked = $this->db->exec($sql);
                if (!$worked) {
                    $error = $this->db->lastErrorMsg();
                    throw new Exception('Cannot add coverage for test ' . $testpath .
                                        ', covered file ' . $path . ': ' . $error . "\nSQL: $sql");
                }
            }
        }
    }

    function begin()
    {
        $this->db->exec('PRAGMA synchronous=OFF'); // make inserts super fast
        $this->db->exec('BEGIN');
    }

    function commit()
    {
        $this->db->exec('COMMIT');
        $this->db->exec('PRAGMA synchronous=NORMAL'); // make inserts super fast
        echo "Compatcing the database\n";
        $this->db->exec('VACUUM');
    }

    /**
     * Retrieve a list of .phpt tests that either have been modified,
     * or the files they access have been modified
     * @return array
     */
    function getModifiedTests()
    {
        // first scan for new .phpt files
        $tests = array();
        foreach (new \RegexIterator(
                    new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($this->testpath,
                                                        0|\RecursiveDirectoryIterator::SKIP_DOTS)
                    ), '/\.phpt$/') as $file
        ) {
            if (strpos((string) $file, '.svn')) {
                continue;
            }

            $tests[] = realpath((string) $file);
        }

        $newtests = array();
        foreach ($tests as $path) {
            if ($path == $this->db->querySingle('SELECT testpath FROM tests WHERE testpath = "' .
                                       $this->db->escapeString($path) . '"')) {
                continue;
            }

            $newtests[] = $path;
        }

        $modifiedTests = $modifiedPaths = array();
        $paths = $this->retrievePaths(1);
        echo "Scanning ", count($paths), " source files";
        foreach ($paths as $path) {
            echo '.';

            $sql = 'SELECT id, hash, issource FROM files WHERE path = "' . $this->db->escapeString($path) . '"';
            $result = $this->db->query($sql);
            while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
                if (!file_exists($path) || md5_file($path) == $res['hash']) {
                    if ($res['issource'] && !file_exists($path)) {
                        $this->db->exec('
                            DELETE FROM files WHERE id = '. $res['id'] .';
                            DELETE FROM coverage WHERE files_id = '. $res['id'] . ';
                            DELETE FROM all_lines WHERE files_id = '. $res['id'] . ';
                            DELETE FROM line_info WHERE files_id = '. $res['id'] . ';');
                    }
                    break;
                }

                $modifiedPaths[] = $path;
                // file is modified, get a list of tests that execute this file
                if ($res['issource']) {
                    $sql = '
                        SELECT t.testpath
                        FROM coverage c, tests t
                        WHERE
                            c.files_id = ' . $res['id'] . '
                          AND
                            t.id = c.tests_id';
                } else {
                    $sql = '
                        SELECT t.testpath
                        FROM coverage_nonsource c, tests t
                        WHERE
                            c.files_id = ' . $res['id'] . '
                          AND
                            t.id = c.tests_id';
                }

                $result2 = $this->db->query($sql);
                while ($res = $result2->fetchArray(SQLITE3_NUM)) {
                    $modifiedTests[$res[0]] = true;
                }

                break;
            }
        }

        echo "done\n";
        echo count($modifiedPaths), ' modified files resulting in ',
            count($modifiedTests), " modified tests\n";
        $paths = $this->retrieveTestPaths();
        echo "Scanning ", count($paths), " test paths";
        foreach ($paths as $path) {
            echo '.';
            $sql = '
                SELECT id, hash FROM tests where testpath = "' .
                $this->db->escapeString($path) . '"';
            $result = $this->db->query($sql);
            while ($res = $result->fetchArray(SQLITE3_ASSOC)) {
                if (!file_exists($path)) {
                    $this->removeOldTest($path, $res['id']);
                    continue;
                }

                if (md5_file($path) != $res['hash']) {
                    $modifiedTests[$path] = true;
                }
            }
        }

        echo "done\n";
        echo count($newtests), ' new tests and ', count($modifiedTests), " modified tests should be re-run\n";
        return array_merge($newtests, array_keys($modifiedTests));
    }
}
<?php
namespace Pyrus\Developer\CoverageAnalyzer\SourceFile {
use Pyrus\Developer\CoverageAnalyzer\Aggregator,
    Pyrus\Developer\CoverageAnalyzer\AbstractSourceDecorator;
class PerTest extends \Pyrus\Developer\CoverageAnalyzer\SourceFile
{
    protected $testname;

    function __construct($path, Aggregator $agg, $testpath, $sourcepath, $testname, $coverage =  true)
    {
        $this->testname = $testname;
        parent::__construct($path, $agg, $testpath, $sourcepath, $coverage);
    }

    function setCoverage()
    {
        $this->coverage = $this->aggregator->retrieveCoverageByTest($this->path, $this->testname);
    }

    function coveredLines()
    {
        $info = $this->aggregator->coverageInfoByTest($this->path, $this->testname);
        return $info[0];
    }

    function render(AbstractSourceDecorator $decorator = null)
    {
        if ($decorator === null) {
            $decorator = new DefaultSourceDecorator('.');
        }
        return $decorator->render($this, $this->testname);
    }

    function coveragePercentage()
    {
        return $this->aggregator->coveragePercentage($this->path, $this->testname);
    }

    function coverageInfo()
    {
        return $this->aggregator->coverageInfoByTest($this->path, $this->testname);
    }
}
}
?>
.ln {background-color:#f6bd0f; padding-right: 4px;}
.cv {background-color:#afd8f8;}
.nc {background-color:#d64646;}
.dead {background-color:#ff8e46;}

ul { list-style-type: none; }

div.bad, div.ok, div.good {
    white-space:pre;
    font-family:courier;
    width: 160px;
    float: left;
    margin-right: 10px;
}
.bad {background-color:#d64646; }
.ok {background-color:#f6bd0f; }
.good {background-color:#588526;}<?php
namespace Pyrus\Developer\CoverageAnalyzer {
    ini_set("display_errors", true);
    session_start();
    $view = new Web\View;
    $rooturl = parse_url($_SERVER["REQUEST_URI"]);
    $rooturl = $rooturl["path"];

    $controller = new Web\Controller($_GET);
    $controller::$rooturl = $rooturl;

    $savant = new \PEAR2\Templates\Savant\Main();
    $savant->setClassToTemplateMapper(new Web\ClassToTemplateMapper);
    $savant->setTemplatePath(__DIR__ . "/../../../../www/CoverageAnalyzer/templates");
    $savant->setEscape("htmlentities");
    try {
        echo $savant->render($controller);
    } catch (Exception $e) {
        var_dump($e);
    }
}=�!}�e�c}���:�s?�   GBMB