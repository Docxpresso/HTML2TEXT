<?php

namespace Docxpresso\HTML2TEXT;

/**
 * Description: HTML to plain text converter
 * URI: http://www.docxpresso.com
 * Version: 1.0
 * Author: No-nonsense Labs
 * License: MIT
 * Copyright 2017 No-nonsense Labs
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
 * THE SOFTWARE.
 */

class HTML2TEXT
{
    
    /**
     * chars used to wrap bold text
     * 
     * @var string
     * @access private
     */
    private $_bold;
    
    /**
     * chars used to separate cells in a table row
     * 
     * @var srting
     * @access private
     */
    private $_cellSeparator;

    /**
     * HTML DOMDocument
     * 
     * @var DOMDocument
     * @access private
     */
    private $_domHTML;
    
    /**
     * tidied up html string
     * 
     * @var string
     * @access private
     */
    private $_html;
    
    /**
     * if set to true will print the alt attribute of the image (if any)
     * 
     * @var boolean
     * @access private
     */
    private $_images;
    
    /**
     * chars used to wrap text in italics
     * 
     * @var string
     * @access private
     */
    private $_italics;
	
    /**
     * this array stores the required current list item numbering
     * 
     * @var array
     * @access private
     */
    private $_list;
    
    /**
     * this array stores the required current list type
     * 
     * @var array
     * @access private
     */
    private $_listType;
    
    /**
     * HTML string to be converted
     * 
     * @var string
     * @access private
     */
    private $_str;

    /**
     * chars used to indent list items
     * 
     * @var srting
     * @access private
     */
    private $_tab;
    
    /**
     * plain text string
     * 
     * @var string
     */
    private $_text;
    
    /**
     * constructor
     *
     * @param string $str
     * @param array $options an array with the following keys an values:
     *      tab: a string of chars that will be used like a "tab". The default
     *      value is "   " (\t may be another standard option)
     *      cellSeparator: a string of chars used to separate content between
     *      contiguous cells in a row. Default value is " || " (\t may be also
     *      a sensible choice)
     *      italics: a string of chars that will wrap text in <i> or <em>. The
     *      default value is an empty string.
     *      bold: a string of chars that will wrap text in <b> or <strong>. The
     *      default value is an empty string.
     *      images: if set to true the alt value associated to the image will
     *      be printed like [img: alt value]. Default value is true.
     */
    public function __construct($str, $options = array()) {
       	$this->_str = $str;
      	$this->_text = '';
        $this->_list = array();
        $this->_listType = array();
        if (isset($options['tab'])) {
            $this->_tab = $options['tab'];
        } else {
            $this->_tab = '  ';
        }
        if (isset($options['cellSeparator'])) {
            $this->_cellSeparator = $options['cellSeparator'];
        } else {
            $this->_cellSeparator = ' || ';
        }
        if (isset($options['italics'])) {
            $this->_italics = $options['italics'];
        } 
        if (isset($options['bold'])) {
            $this->_bold = $options['bold'];
        }
        if (isset($options['images'])) {
            $this->_images = $options['images'];
        } else {
            $this->_images = true;
        }
    }
    
    /**
     * returns the plain text version of the HTML code
     *
     * @return string
     * @access public
     */
    public function plainText(){
        if ($this->_isHTML($this->_str)){
            $this->_parseHTML();
            $output = $this->_text;
        } else {
            $output = $this->_str;
        }
        return $output;
    }
    
    /**
     * echo the text version of the HTML code
     *
     * @param string $string
     * @return void
     * @access public
     */
    public function printText(){
        echo $this->plainText();
    }
    
    /**
     * generate the list "numberings"
     *
     * @param integer $level
     * @return void
     * @access private
     */
    private function _generateLI($level)
    {
        if (isset($this->_list[$level])) {
            if (isset($this->_listType[$level])) {
                $type = $this->_listType[$level];
            } else {
                $type = 'unordered';
            }
            if ($type == 'ordered') {
                $this->_generateLIOrdered($level);
            } else {
                $this->_generateLIUnordered($level);
            }
        }	
    }
	
    /**
     * generate a particular numbering
     *
     * @param integer $level
     * @return void
     * @access private
     */
    private function _generateLIOrdered($level)
    {
        $chstr = '';
        $order = count($this->_list[$level]);	
        $chstr .= str_repeat($this->_tab, $level);
        if ($level == 1) {
            $chstr .= $order . '.';
        } else {
            $preorder = '';
            for ($k = 1; $k < $level; $k++) {
                $preorder .= count($this->_list[$k]) . '.';
            }
            $chstr .= $preorder . $order . '.';
        }
        $this->_text .= $chstr . ' ';
    }
	
    /**
     * generate a particular unorderd list item
     *
     * @param integer $level
     * @return void
     * @access private
     */
    private function _generateLIUnordered($level)
    {
        $chstr = '';
        $order = count($this->_list[$level]);	
        $chstr .= str_repeat($this->_tab, $level);
        $chstr .= str_repeat('*', $level);
        $this->_text .= $chstr . ' ';
    }
        
    /**
     * checks if a string contains HTML code
     *
     * @param string $string
     * @return boolean
     * @access private
     */
    private function _isHTML($string)
    {
        $html = preg_match("/<[^<]+>/",$string);
        if (empty($html)) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * This method recursively parses nodes that are not list or HTML
     *
     * @param DOMNode $node
     * @param integer $level
     * @return void
     * @access private
     */
    private function _parseChilds($node, $level)
    {
        if ($node->hasChildNodes()) {
            $childs = $node->childNodes;
            foreach ($childs as $child) {
                $this->_parseHTMLNode($child, $level);
            }
        }
    }
    
    /**
     * parses the provided HTML
     *
     * @return void
     * @access private
     */
    private function _parseHTML()
    {
        $this->_tidyHTML();
        $this->_domHTML = new \DOMDocument();
        $this->_domHTML->preserveWhiteSpace = false;
        //the following option tries to recover poorly formed documents
        $this->_domHTML->recover = true;
        $this->_domHTML->preserveWhiteSpace = true;
        $this->_domHTML->formatOutput = false;
        $this->_domHTML->loadXML($this->_html);
        $root = $this->_domHTML->documentElement;
        $this->_parseHTMLNode($root);
    }
    
    /**
     * This method translate HTML nodes into plain text
     *
     * @param DOMNode $node
	 * @param integer $level
     * @return void
     * @access private
     */
    private function _parseHTMLNode($node, $level = 0)
    { 
        $stopNodes = array('style' => true,
                   'head' => true,
                   'script' => true,
                    );
        
        $tag = strtolower($node->nodeName);
        if (isset($stopNodes[$tag])) {
           //we should stop recursive node parsing here 
        } else {
            switch ($tag) {
                case '#text':
                    $this->_text .= $node->nodeValue;
                    break;
                case 'a':
                    $href = $node->getAttribute('href');
                    $this->_parseChilds($node, $level);
                    $this->_text .= ' [' . $href . '] ';
                    break;
                case 'b':
                case 'strong':
                    if (isset($this->_bold)){
                        $this->_text .= $this->_bold;
                        $this->_parseChilds($node, $level);
                        $this->_text .= $this->_bold;
                    } else {
                        $this->_parseChilds($node, $level);
                    }
                    break;
                case 'br':
                    $this->_text .= PHP_EOL;
                    break;
                case 'dd':
                    $this->_text .= $this->_tab;
                    $this->_parseChilds($node, $level);
                    $this->_text .= PHP_EOL;
                    break;
                case 'dt':
                    $this->_parseChilds($node, $level);
                    $this->_text .= PHP_EOL;
                    break;
                case 'h1':
                case 'h2':
                case 'h3':
                case 'h4':
                case 'h5':
                case 'h6':
                    $this->_parseChilds($node, $level);
                    $numChars = strlen($node->nodeValue);
                    $this->_text .= PHP_EOL;
                    $this->_text .= str_repeat("=", $numChars);
                    $this->_text .= PHP_EOL;
                    break;
                case 'i':
                case 'em':
                    if (isset($this->_italics)){
                        $this->_text .= $this->_italics;
                        $this->_parseChilds($node, $level);
                        $this->_text .= $this->_italics;
                    } else {
                        $this->_parseChilds($node, $level);
                    }
                    break;
                case 'img':
                    $alt = $node->getAttribute('alt');
                    if (!empty($alt) && $this->_images){
                        $this->_text .= ' [img: ' . $alt . '] ';
                    }
                    break;
                case 'li':
                    $this->_list[$level][] = true;
                    $this->_generateLI($level);
                    $this->_parseChilds($node, $level);
                    //we have to check if the list item is the last one
                    //of a nested ul not to add extra carriage returns
                    $nextNode = $node->nextSibling;
                    $nested = $node->parentNode->parentNode;
                    if (!$nextNode && $nested->nodeName == 'li') {
                        //do not add a carriage return
                    } else {
                        $this->_text .= PHP_EOL;
                    }
                    break;
                case 'ol':
                    if ($level == 0) {
                        $this->_list = array();
                        $this->_listType = array();
                    } else {
                        $this->_text .= PHP_EOL;
                    }
                    $level++;
                    $this->_level[$level] = array();
                    $this->_unsetChildLists($level);
                    $this->_listType[$level] = 'ordered';
                    $this->_parseChilds($node, $level);
                    break;
                case 'p':
                    $this->_parseChilds($node, $level);
                    $nextNode = $node->nextSibling;
                    $nested = $node->parentNode;
                    if (!$nextNode && 
                        ($nested->nodeName == 'td' || $nested->nodeName == 'th' || $nested->nodeName == 'li')) {
                        //do not add a carriage return
                    } else {
                        $this->_text .= PHP_EOL;
                    }
                    break;
                case 'td':
                case 'th':
                    $this->_parseChilds($node, $level);
                    $this->_text .= $this->_cellSeparator;
                    break;
                case 'tr':
                    $this->_parseChilds($node, $level);
                    $this->_text .= PHP_EOL;
                    break;
                case 'ul':
                    if ($level == 0) {
                        $this->_list = array();
                        $this->_listType = array();
                    } else {
                        $this->_text .= PHP_EOL;
                    }
                    $level++;
                    $this->_level[$level] = array();
                    $this->_unsetChildLists($level);
                    $this->_listType[$level] = 'unordered';
                    $this->_parseChilds($node, $level);
                    break;
                default:
                    $this->_parseChilds($node, $level); 
                    break;					
            }
        }
    }
    
    /**
     * use tidy to clean up HTML
     *
     * @return srting
     * @access private
     */
    private function _tidyHTML()
    {
        try{
            $tidy = new \tidy();

            $config = array(
                    'force-output' => true,
                    'indent' => false,
                    'enclose-block-text' => true,
                    'enclose-text' => true,
                    'numeric-entities' => true,
                    'vertical-space' => false,
                    'wrap' => 0,
                    'markup' => false,
                    'bare' => true,
                    'output-xhtml' => true,
            );
            $tidy = \tidy_parse_string($this->_str, $config, 'utf8');
            $tidiedHTML = $tidy->html();
            $this->_html = $tidiedHTML->value;
            $this->_html = preg_replace("/(\r\n)+|\r+|\n+|\t+/i", "", $this->_html);
        }
        catch(Exception $e){
            $this->_html = 'error';
            throw new Exception('Tidy threw a fatal error.');
        }
    }

    /**
     * Clers deeper lists numenberings and styles
     *
     * @param integer $level
     * @return void
     * @access private
     */
    private function _unsetChildLists($level)
    {
    	for ($k = $level; $k < 10; $k++) {
            if (isset($this->_list[$k])) {
                unset($this->_list[$k]);
            }
            if (isset($this->_listType[$k])) {
                unset($this->_listtype[$k]);
            }
    	}

    }

}
