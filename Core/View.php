<?php

namespace Core;

use App\Auth;
use App\Flash;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class View
{
    /**
     * Render a view file
     *
     * @param string $view
     * @param array $data
     * @throws Exception
     */
    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $view_file = dirname(__DIR__) . "/App/Views/$view.php";

        if (is_readable($view_file))
            require_once $view_file;
        else
            throw new Exception("$view_file not found");
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template
     * @param array $data
     */
    public static function renderTemplate(string $template, $data = []): void
    {
        static $twig = null;

        if ($twig === null){
            $loader = new FilesystemLoader(dirname(__DIR__) . "/App/Views");
            $twig = new Environment($loader);

            // Twig globals
            $twig->addGlobal('is_logged_in', Auth::isLoggedIn());
            $twig->addGlobal('current_user', Auth::getUser());
            $twig->addGlobal('flash_messages', Flash::getMessages());
        }

        try {
            echo $twig->render($template . '.twig', $data);
        } catch (LoaderError $e) {
            Error::exceptionHandler($e);
        } catch (RuntimeError $e) {
            Error::exceptionHandler($e);
        } catch (SyntaxError $e) {
            Error::exceptionHandler($e);
        }
    }
}