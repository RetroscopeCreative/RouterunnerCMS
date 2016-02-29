/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.filebrowserBrowseUrl = routerunner.settings.BASE + routerunner.settings.BACKEND_ROOT + 'backend/thirdparty/kcfinder/browse.php?opener=ckeditor&type=files';
    config.filebrowserImageBrowseUrl = routerunner.settings.BASE + routerunner.settings.BACKEND_ROOT + 'backend/thirdparty/kcfinder/browse.php?opener=ckeditor&type=images';
    config.filebrowserFlashBrowseUrl = routerunner.settings.BASE + routerunner.settings.BACKEND_ROOT + 'backend/thirdparty/kcfinder/browse.php?opener=ckeditor&type=flash';
    config.filebrowserUploadUrl = routerunner.settings.BASE + routerunner.settings.BACKEND_ROOT + 'backend/thirdparty/kcfinder/upload.php?opener=ckeditor&type=files';
    config.filebrowserImageUploadUrl = routerunner.settings.BASE + routerunner.settings.BACKEND_ROOT + 'backend/thirdparty/kcfinder/upload.php?opener=ckeditor&type=images';
    config.filebrowserFlashUploadUrl = routerunner.settings.BASE + routerunner.settings.BACKEND_ROOT + 'backend/thirdparty/kcfinder/upload.php?opener=ckeditor&type=flash';

};
