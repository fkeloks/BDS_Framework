<?php

namespace BDSCore\Debug;

/**
 * Class DebugBar
 * @package BDSCore\Debug
 */
class DebugBar
{

    /**
     * @var array
     */
    private static $items = [];

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public static function pushElement(string $key, $value) {
        self::$items[$key] = $value;
    }

    /**
     * @return array
     */
    public static function getElements(): array {
        return self::$items;
    }

    /**
     * @param string|null $file
     * @return string
     * @throws DebugException
     */
    public static function insertDebugBar(string $file = null): string {
        if ($file != null) {
            $elements = self::$items;
            $elementsHtml = '';
            foreach ($elements as $el => $e) {
                (is_array($e) || is_bool($e) || is_int($e)) ? $e = var_export($e, true) : null;
                if (!is_string($e)) {
                    throw new DebugException('Invalid variable type for the insertElement() function.');
                }
                $elementsHtml = $elementsHtml . "<span>- <b>$el:</b> $e</span><br>";
            }
            $debugBar = '<style>div.openDebugBar{position: fixed; display: block; bottom: 45px; left: 45px; height: 45px; width: 45px; border: none; outline: none; border-radius: 50%; z-index: 9998; background-color: #3B445B; text-align: center; transition: all 300ms}div.openDebugBar .menu-bar{display: block; position: absolute; top: 50%; left: 50%; transform: translateX(-50%) translateY(-50%); z-index: 9999; height: 3px; width: 25px; background-color: #FFF; border-radius: 5px}div.openDebugBar .menu-bar::before, div.openDebugBar .menu-bar::after{display: block; position: absolute; content: ""; background-color: #FFF; border-radius: 5px; height: 3px; width: 25px; transform: translateY(-7px)}div.openDebugBar .menu-bar::after{transform: translateY(7px)}div.openDebugBar .menu-label{display: block; position: absolute; top: 50px; left: -10px; user-select: none; color: #3B445B}div.openDebugBar:hover{background-color: #565f86; cursor: pointer}div.openDebugBar.is-open{background-color: #565f86; transform: translateX(165px)}div.debugBar{position: fixed; z-index: 9990; top: 0; border-right: 1px solid #FFF; line-height: 150%; bottom: 0; left: -275px; height: 100%; width: 274px; background-color: #3B445B; transition: transform 300ms; color: #FFF; padding-top: 20px}div.debugBar h3{font-size: 24px;}div.debugBar span{font-size: 15px;}div.debugBar a{text-decoration: none; color: inherit;}div.debugBar hr{margin-bottom: 15px; color: #FFF;}div.debugBar.is-open{padding: 20px; transform: translateX(275px)}</style> <div class="openDebugBar"><span class="menu-label">DebugBar</span> <span class="menu-bar"></span></div><div class="debugBar"><h3>BDS Framework V1.0</h3> <hr> <span id="loadingTime">- <b>LoadingTime: </b></span><br>' . $elementsHtml . '</div><script>var openButton=document.querySelector("div.openDebugBar"); var debugBar=document.querySelector("div.debugBar"); var cookContent=document.cookie, cookEnd, i, j; var sName="BDS_loadingTime="; for (i=0, c=cookContent.length; i < c; i++){j=i + sName.length; if (cookContent.substring(i, j)==sName){cookEnd=cookContent.indexOf(";", j); if (cookEnd==-1){cookEnd=cookContent.length;}var loadingTime=decodeURIComponent(cookContent.substring(j, cookEnd));}}document.getElementById("loadingTime").appendChild(document.createTextNode(loadingTime)); openButton.addEventListener("click", function (e){e.preventDefault(); if (debugBar.classList.contains("is-open")){debugBar.classList.remove("is-open"); openButton.classList.remove("is-open");}else{debugBar.classList.add("is-open"); openButton.classList.add("is-open");}}); </script>';
            $file = preg_replace('/(<body>)/', '${1}' . $debugBar, $file);

            return $file;
        }
        throw new \BDSCore\Debug\DebugException('File path is not specified in insertDebugBar\'function');
    }

}