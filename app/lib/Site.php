<?php
namespace anet\core\lib;

/**
 * Site builder class.
 *
 * Generates a view-based display
 * for both the frontend and backend.
 *
 * @author      Jon Belelieu
 * @link        http://www.ascadnetworks.com/
 * @license     GNU General Public License v3.0
 * @link        http://www.gnu.org/licenses/gpl.html
 * @date        2013-12-05
 * @version     v1.0
 * @project     ANET Framework
 */

class Site {

    protected $input, $page, $scope, $pathComponents, $loadView, $dataIn;
    protected $area, $options, $rendered, $viewFolder;
    public $error;

    public function __construct($dataIn = array(), $options = array())
    {
        $this->data = $dataIn;
        $this->options = $options;
        $this->determineLocation();
        $this->render();
    }

    public function __toString()
    {
        return $this->rendered;
    }

    protected function determineLocation()
    {
        if (! empty($_GET['anet_in'])) {
            $this->input = $_GET['anet_in'];
            $this->pathComponents = explode('/', trim($_GET['anet_in']));
        } else {
            $this->pathComponents = array();
        }
        $this->prepPaths();
        if (! empty($_GET['anet_in'])) {
            $this->breakUpPath();
        } else {
            $this->page = 'index';
        }
    }

    protected function prepPaths()
    {
        $this->viewFolder = \anet\conf\ROOT;
        $this->viewFolder .= DIRECTORY_SEPARATOR . 'core';
        if ($this->findDashboard()) {
            $this->area = 'backend';
        } else {
            $this->area = 'frontend';
        }
        $this->viewFolder .= DIRECTORY_SEPARATOR . $this->area . DIRECTORY_SEPARATOR . 'view';
    }

    protected function breakUpPath()
    {
        $last_character = substr($_GET['anet_in'], strlen($_GET['anet_in'])-1);
        if ($last_character == '/') {
            $this->page = 'index';
        } else {
            $this->page = array_pop($this->pathComponents);
        }
        $this->viewFolder .= $this->viewPath();
        $this->loadView = $this->viewFolder . DIRECTORY_SEPARATOR . $this->page . '.php';
        if (! file_exists($this->loadView)) {
            $this->error = '1';
        } else {
            $this->error = '0';
        }
        if (empty($this->page)) {
            $this->page = 'index';
        }
    }

    protected function viewPath()
    {
        $add = '';
        foreach ($this->pathComponents as $pathItem) {
            if (! empty($pathItem)) {
                $add .= DIRECTORY_SEPARATOR . $pathItem;
            }
        }
        return $add;
    }

    protected function findDashboard()
    {
        if ($this->pathComponents['0'] == \anet\conf\ADMIN_FOLDER) {
            $removeDash = array_shift($this->pathComponents);
            return true;
        } else {
            return false;
        }
    }

    protected function render()
    {
        if (empty($this->options['skip_header'])) {
            $this->rendered = $this->processTemplate('header');
        }
        $this->rendered .= $this->processTemplate($this->page);
        if (empty($this->options['skip_footer'])) {
            $this->rendered .= $this->processTemplate('footer');
        }
    }

    protected function processTemplate($viewId)
    {
        $file = $this->viewFolder . DIRECTORY_SEPARATOR . $viewId . '.php';
        if (file_exists($file)) {
            ob_start();
            require $file;
            $content = ob_get_contents();
            ob_end_clean();
            return $this->processCallers($content);
        } else {
            $file = $this->viewFolder . DIRECTORY_SEPARATOR . '404.php';
            $this->error = '1';
        }
    }

    protected function processCallers($content)
    {
        foreach ($this->data as $caller => $value) {
            $content = str_replace('%' . $caller . '%', $value, $content);
        }

        $baseChanges = array(
            'url' => \anet\conf\URL,
            'admin' => \anet\conf\ADMIN_FOLDER,
        );
        foreach ($baseChanges as $caller => $value) {
            $content = str_replace('%' . $caller . '%', $value, $content);
        }
        return $content;
    }

}