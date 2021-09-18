<?php
//toto je soubor, ktery vyhleda zdrojove kody knihovny, vyhleda to nasi slozku shortcodes a potom to metodou precte zadane html a nahradi to nasi slozkou shortcodes
class __ISPsr4ClassLoader
{
	private static $prefixes = [];

	public static function AddPrefix($prefix, $baseDir)
	{
		$prefix = trim($prefix, '\\').'\\';
		$baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		self::$prefixes[] = array($prefix, $baseDir);
	}

	public static function __autoload($class)
	{
		$class = ltrim($class, '\\');

		foreach (self::$prefixes as $current) {
			list($currentPrefix, $currentBaseDir) = $current;
			if (strpos($class, $currentPrefix) === 0) {
				$classWithoutPrefix = substr($class, strlen($currentPrefix));
				$file = $currentBaseDir.str_replace('\\', DIRECTORY_SEPARATOR, $classWithoutPrefix).'.php';
				if (file_exists($file)) {
					require $file;
					return true;
				}
			}
		}
		return false;
	}
}

spl_autoload_register(array('__ISPsr4ClassLoader', '__autoload'));

__ISPsr4ClassLoader::AddPrefix('Thunder\\Shortcode\\', 'vendor/Shortcode/src/');

class ShortcodeProcessor
{
	static protected $initialized = false;
	static protected $handlers;
	static protected $events;
	static protected $processor;
	
	static function init()
	{
		if (!ShortcodeProcessor::$initialized) {
			ShortcodeProcessor::$handlers = new \Thunder\Shortcode\HandlerContainer\HandlerContainer();
			
			ShortcodeProcessor::$handlers->add('raw', function(\Thunder\Shortcode\Shortcode\ShortcodeInterface $s) {
				return $s->getContent();
			});
			ShortcodeProcessor::$events = new \Thunder\Shortcode\EventContainer\EventContainer();
			ShortcodeProcessor::$events->addListener(\Thunder\Shortcode\Events::FILTER_SHORTCODES, new \Thunder\Shortcode\EventHandler\FilterRawEventHandler(['raw']));
			ShortcodeProcessor::$processor = new \Thunder\Shortcode\Processor\Processor(new \Thunder\Shortcode\Parser\RegularParser(), ShortcodeProcessor::$handlers);
			ShortcodeProcessor::$processor = ShortcodeProcessor::$processor->withEventContainer(ShortcodeProcessor::$events);
			
			foreach (scandir('my-shortcodes/') as $filename) {
				$matches = array();
				if (preg_match('/^(.+)\\.php$/', $filename, $matches)) {
					$shortcodeKey = $matches[1];
					ShortcodeProcessor::$handlers->add($shortcodeKey, function(\Thunder\Shortcode\Shortcode\ShortcodeInterface $shortcode) use ($shortcodeKey, $filename) {
						
						ob_start();
						require 'my-shortcodes/'.$filename;
						$output = ob_get_contents();
						ob_end_clean();
						return $output;
					});
				}
			}
		}
	}
	
	static function process($text)
	{
		ShortcodeProcessor::init();
		
		return ShortcodeProcessor::$processor->process($text);
	}
}
