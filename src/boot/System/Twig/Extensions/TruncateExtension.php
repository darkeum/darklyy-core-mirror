<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Boot\System\Twig\Extensions;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use \DOMText;
use \DOMDocument;
use \Boot\System\Twig\DOMDocument\DOMWordsIterator;
use \Boot\System\Twig\DOMDocument\DOMLettersIterator;

final class TruncateExtension extends AbstractExtension
{

    /**
     * @return array Returns the list of filters supplied by this extension.
     */
    public function getFilters()
    {


        $truncateWords = new TwigFilter(
            'truncate_words',
            array($this, 'truncateWords'),
            array(
                'is_safe' => array('html'),
            )
        );

        $truncateLetters = new TwigFilter(
            'truncate_letters',
            array($this, 'truncateLetters'),
            array(
                'is_safe' => array('html'),
            )
        );

        return array(
            'truncate_letters' => $truncateWords,
            'truncate_words'   => $truncateLetters,
        );
    }

    public function truncateWords($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true)
    {

        // if the plain text is shorter than the maximum length, return the whole text
        if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        // splits all html-tags to scanable lines
        preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
        $total_length = strlen($ending);
        $open_tags = array();
        $truncate = '';
        foreach ($lines as $line_matchings) {
            // if there is any html-tag in this line, handle it and add it (uncounted) to the output
            if (!empty($line_matchings[1])) {
                // if it's an "empty element" with or without xhtml-conform closing slash
                if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                    // do nothing
                    // if tag is a closing tag
                } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                    // delete tag from $open_tags list
                    $pos = array_search($tag_matchings[1], $open_tags);
                    if ($pos !== false) {
                        unset($open_tags[$pos]);
                    }
                    // if tag is an opening tag
                } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                    // add tag to the beginning of $open_tags list
                    array_unshift($open_tags, strtolower($tag_matchings[1]));
                }
                // add html-tag to $truncate'd text
                $truncate .= $line_matchings[1];
            }
            // calculate the length of the plain text part of the line; handle entities as one character
            $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
            if ($total_length + $content_length > $length) {
                // the number of characters which are left
                $left = $length - $total_length;
                $entities_length = 0;
                // search for html entities
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                    // calculate the real length of all entities in the legal range
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entities_length <= $left) {
                            $left--;
                            $entities_length += strlen($entity[0]);
                        } else {
                            // no more characters left
                            break;
                        }
                    }
                }
                $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                // maximum lenght is reached, so get off the loop
                break;
            } else {
                $truncate .= $line_matchings[2];
                $total_length += $content_length;
            }
            // if the maximum length is reached, get off the loop
            if ($total_length >= $length) {
                break;
            }
        }

        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if ($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
        return $truncate;
    }
    /**
     * Safely truncates HTML by a given number of words.
     * @param  string  $html     Input HTML.
     * @param  integer $limit    Limit to how many words we preserve.
     * @param  string  $ellipsis String to use as ellipsis (if any).
     * @return string            Safe truncated HTML.
     */
    // public function truncateWords($html, $limit = 0, $ellipsis = "")
    // {
    //     if ($limit <= 0) {
    //         return $html;
    //     }

    //     $dom = $this->htmlToDomDocument($html);
    //     // dd($dom);

    //     // Grab the body of our DOM.
    //     $body = $dom->getElementsByTagName("body")->item(0);

    //     // Iterate over words.
    //     $words = new DOMWordsIterator($body);
    //     var_dump($body);
    //     exit;
    //     foreach ($words as $word) {

    //         // If we have exceeded the limit, we delete the remainder of the content.
    //         if ($words->key() >= $limit) {

    //             // Grab current position.
    //             $currentWordPosition = $words->currentWordPosition();
    //             $curNode = $currentWordPosition[0];
    //             $offset = $currentWordPosition[1];
    //             $words = $currentWordPosition[2];

    //             $curNode->nodeValue = substr(
    //                 $curNode->nodeValue,
    //                 0,
    //                 $words[$offset][1] + strlen($words[$offset][0])
    //             );

    //             self::removeProceedingNodes($curNode, $body);

    //             if (!empty($ellipsis)) {
    //                 self::insertEllipsis($curNode, $ellipsis);
    //             }

    //             break;
    //         }


    //     }

    //     return $dom->saveHTML();
    // }

    /**
     * Safely truncates HTML by a given number of letters.
     * @param  string  $html     Input HTML.
     * @param  integer $limit    Limit to how many letters we preserve.
     * @param  string  $ellipsis String to use as ellipsis (if any).
     * @return string            Safe truncated HTML.
     */
    public function truncateLetters($html, $limit = 0, $ellipsis = "")
    {
        if ($limit <= 0) {
            return $html;
        }

        $dom = $this->htmlToDomDocument($html);

        // Grab the body of our DOM.
        $body = $dom->getElementsByTagName("body")->item(0);

        // Iterate over letters.
        $letters = new DOMLettersIterator($body);
        foreach ($letters as $letter) {

            // If we have exceeded the limit, we want to delete the remainder of this document.
            if ($letters->key() >= $limit) {

                $currentText = $letters->currentTextPosition();
                $currentText[0]->nodeValue = substr($currentText[0]->nodeValue, 0, $currentText[1] + 1);
                self::removeProceedingNodes($currentText[0], $body);

                if (!empty($ellipsis)) {
                    self::insertEllipsis($currentText[0], $ellipsis);
                }

                break;
            }
        }

        return $dom->saveHTML();
    }

    /**
     * Builds a DOMDocument object from a string containing HTML.
     * @param string HTML to load
     * @returns DOMDocument Returns a DOMDocument object.
     */
    public function htmlToDomDocument($html)
    {
        // Transform multibyte entities which otherwise display incorrectly.
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        // dd($html);
        // Internal errors enabled as HTML5 not fully supported.
        libxml_use_internal_errors(true);

        // Instantiate new DOMDocument object, and then load in UTF-8 HTML.
        $dom = new DOMDocument();
        $dom->encoding = 'UTF-8';
        $dom->loadHTML($html);

        return $dom;
    }

    /**
     * Removes all nodes after the current node.
     * @param  DOMNode|DOMElement $domNode
     * @param  DOMNode|DOMElement $topNode
     * @return void
     */
    private static function removeProceedingNodes($domNode, $topNode)
    {
        $nextNode = $domNode->nextSibling;

        if ($nextNode !== null) {
            self::removeProceedingNodes($nextNode, $topNode);
            $domNode->parentNode->removeChild($nextNode);
        } else {
            //scan upwards till we find a sibling
            $curNode = $domNode->parentNode;
            while ($curNode !== $topNode) {
                if ($curNode->nextSibling !== null) {
                    $curNode = $curNode->nextSibling;
                    self::removeProceedingNodes($curNode, $topNode);
                    $curNode->parentNode->removeChild($curNode);
                    break;
                }
                $curNode = $curNode->parentNode;
            }
        }
    }

    /**
     * Inserts an ellipsis
     * @param  DOMNode|DOMElement $domNode  Element to insert after.
     * @param  string             $ellipsis Text used to suffix our document.
     * @return void
     */
    private static function insertEllipsis($domNode, $ellipsis)
    {
        $avoid = array('a', 'strong', 'em', 'h1', 'h2', 'h3', 'h4', 'h5'); //html tags to avoid appending the ellipsis to

        if (in_array($domNode->parentNode->nodeName, $avoid) && $domNode->parentNode->parentNode !== null) {
            // Append as text node to parent instead
            $textNode = new DOMText($ellipsis);

            if ($domNode->parentNode->parentNode->nextSibling) {
                $domNode->parentNode->parentNode->insertBefore($textNode, $domNode->parentNode->parentNode->nextSibling);
            } else {
                $domNode->parentNode->parentNode->appendChild($textNode);
            }
        } else {
            // Append to current node
            $domNode->nodeValue = rtrim($domNode->nodeValue) . $ellipsis;
        }
    }

    /**
     * Returns the name of this extension.
     * @return string
     */
    public function getName()
    {
        return 'truncate_extension';
    }
}
