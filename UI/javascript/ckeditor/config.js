/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
    //config.enterMode = CKEDITOR.ENTER_BR;
    config.language = 'de';
    
    //config.toolbarGroupCycling = true;
    config.resize_dir = 'vertical';
    config.skin='office2013';
    config.mathJaxLib = 'javascript/MathJax/MathJax.js?config=TeX-AMS_HTML';

	//config.uiColor = '#f2f0f0';
    config.toolbarGroups = [
    { name: 'styles' },
    { name: 'colors' },
    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
    { name: 'editing',     groups: [ 'find', 'selection'] },
    '/',
    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
    { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
    { name: 'insert' }
    ];
};

/*MathJax.Hub.Config({
    jax: ["input/AsciiMath","output/HTML-CSS"]
  });*/