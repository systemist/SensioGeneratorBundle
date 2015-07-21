<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sensio\Bundle\GeneratorBundle\Generator;

use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Generator is the base class for all generators.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Generator
{
    private $skeletonDirs;
    private static $output;

    public function __construct()
    {
        self::$output = new ConsoleOutput();
    }

    /**
     * Sets an array of directories to look for templates.
     *
     * The directories must be sorted from the most specific to the most
     * directory.
     *
     * @param array $skeletonDirs An array of skeleton dirs
     */
    public function setSkeletonDirs($skeletonDirs)
    {
        $this->skeletonDirs = is_array($skeletonDirs) ? $skeletonDirs : array($skeletonDirs);
    }

    protected function render($template, $parameters)
    {
        $twig = $this->getTwigEnvironment();

        return $twig->render($template, $parameters);
    }

    /**
     * Get the twig environment that will render skeletons.
     *
     * @return \Twig_Environment
     */
    protected function getTwigEnvironment()
    {
        return new \Twig_Environment(new \Twig_Loader_Filesystem($this->skeletonDirs), array(
            'debug' => true,
            'cache' => false,
            'strict_variables' => true,
            'autoescape' => false,
        ));
    }

    protected function renderFile($template, $target, $parameters)
    {
        if (!is_dir(dirname($target))) {
            self::mkdir(dirname($target));
        }

        return self::dump($target, $this->render($template, $parameters));
    }

    public static function mkdir($dir, $mode = 0777, $recursive = true)
    {
        mkdir($dir, $mode, $recursive);
        self::$output->writeln(sprintf('  <fg=green>[+dir]   </> %s', self::relativizePath($dir)));
    }

    public static function dump($filename, $content)
    {
        if (file_exists($filename)) {
            self::$output->writeln(sprintf('  <fg=yellow>[changed]</> %s', self::relativizePath($filename)));
        } else {
            self::$output->writeln(sprintf('  <fg=green>[+file]</>   %s', self::relativizePath($filename)));
        }

        return file_put_contents($filename, $content);
    }

    private static function relativizePath($absoluteDir)
    {
        return str_replace(getcwd(), '.', $absoluteDir);
    }
}
