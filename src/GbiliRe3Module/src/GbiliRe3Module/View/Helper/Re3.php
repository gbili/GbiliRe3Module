<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace GbiliRe3Module\View\Helper;

/**
 * View helper uses both Symlink resolver and
 * Scriptalicious for registering and rendering scripts
 * REsolve, REquire, REnder
 * Usage within view: $this->resolveScript($symlink)->requireAndRenderScript('script_hanlde', $params)
 */
class Re3 extends \Zend\View\Helper\AbstractHelper
{
    /**
     * Association of identifiers to content
     * @var array
     */
    protected $scriptalicious;

    protected $symlink;

    public function __construct(\Gbili\Stdlib\Scriptalicious $scriptalicious)
    {
        $this->scriptalicious = $scriptalicious;
    }

    /**
     * Given an identifier, return the associated content
     *
     * @param string
     * @return mixed
     */
    public function __invoke($symlink=null)
    {
        if (null !== $symlink) {
            $this->symlink = $symlink;
            return $this;
        }
        return $this->scriptalicious;
    }

    /**
     * Proxy Re3 but add the symlink functionality.
     * Re3 needs the script content as second param to 
     * addInline. We use symlink to resolve the path to the content
     * @param array $params contains the variables used in required file
     */
    public function requireAndRender($scriptHandle, $params=array())
    {
        if (null === $this->symlink) {
            throw new \Exception('you need to __invoke using first parameter as symlink in or for the view helper to work');
        }

        $symlinkedContentOrFile = $this->view->resolveSymlink($this->symlink);
        $this->symlink = null;

        if (!file_exists($symlinkedContentOrFile)) {
            throw new \Exception('Not able to resolve symlink, make sure you add it to your symlink config array');
        }

        extract($params, EXTR_PREFIX_SAME, "scriptalicious");
        require_once $symlinkedContentOrFile;

        if (!$this->scriptalicious->hasScript($scriptHandle)) {
            throw new \Exception('Your symlinked file should have registered a script in scriptalicious with identifier: ' . $scriptHandle);
        }

        return $this->scriptalicious->renderScriptAndDependencies($scriptHandle);
    }
}
