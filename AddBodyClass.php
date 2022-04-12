<?php
/*
    Copyright 2012 Povilas Kanapickas <tir5c3@yahoo.co.uk>
    Copyright 2016 Will Stott <willstott101@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use SMW\ApplicationFactory;

$wgExtensionCredits['parserhook'][] = array(
    'path'           => __FILE__,
    'name'           => 'AddSMWBodyClass',
    'author'         => 'Povilas Kanapickas & Will Stott & Bertrand Gorge',
    'descriptionmsg' => 'addbodyclass_desc',
    'url'            => 'https://github.com/neayi/AddSMWBodyClass',
    'version'        => '1.2',
);

$wgExtensionMessagesFiles['AddBodyClassMagic'] = dirname( __FILE__ ) . '/' . 'AddBodyClass.i18n.magic.php';
$wgExtensionMessagesFiles['AddBodyClass'] = dirname( __FILE__ ) . '/' . 'AddBodyClass.i18n.php';

$wgHooks['OutputPageParserOutput'][] = 'AddBodyClass::onOutputPageParserOutput';
$wgHooks['OutputPageBodyAttributes'][] = 'AddBodyClass::add_attrs';

$wgCategoriesAsBodyClasses = false;

class AddBodyClass {

    static protected $classes = '';

    /**
     * Gets the property $wgSMWBodyClassesAttribute (please replace spaces with underscore) and store it in the static $class var.
     */
    static function onOutputPageParserOutput( OutputPage $out, ParserOutput $parserOutput )
    {
        global $wgSMWBodyClassesAttribute;

        global $wgTitle;
        $parserData = ApplicationFactory::getInstance()->newParserData(
            $wgTitle,
            $parserOutput
        );

        if (empty($wgSMWBodyClassesAttribute))
        {
            echo "Please define <code>\$wgSMWBodyClassesAttribute = 'A_un_type_de_page';</code> in LocalSettings.php";
        }

        $wgSMWBodyClassesAttribute = str_replace(' ', '_', $wgSMWBodyClassesAttribute);

        // Access the SemanticData object
        $semdata = $parserData->getSemanticData();

        foreach($semdata->getProperties() as $key => $property)
        {
            if ($key == $wgSMWBodyClassesAttribute)
            {
                $typeDePage = $semdata->getPropertyValues($property);
                self::$classes .= ' '. str_replace(' ', '-', $typeDePage[0]);
            }
        }

        return true;
    }

    static function add_attrs($out, $sk, &$bodyAttrs)
    {
        global $wgCategoriesAsBodyClasses;

        if (self::$classes !== '') {
            $bodyAttrs['class'] .= self::$classes;
        }

        if ($wgCategoriesAsBodyClasses) {
            foreach ($out->getCategories() as $categoryName) {
                $safeCategoryName = str_replace(array('.', ' '), '_', $categoryName);
                $bodyAttrs['class'] .= ' cat-' . $safeCategoryName . ' icat-' . strtolower($safeCategoryName);
            }
        }

        return true;
    }

}
