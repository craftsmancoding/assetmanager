Asset Manager
=============

Asset Manager Media Library for MODX Revolution

This package creates a custom database table to store asset information so you can add assets to any 
MODX page.  The result can be similar to adding multiple image Template Variables to a template (e.g.
using MIGX), but the user interface and backend data model is cleaner.


Features:

- Supports complex search criteria to find assets 
- Galleries of Images
- Drag and drop images to upload them using Dropzone
- Crop and resize images via the manager UI using jCrop
- Output filters for easy image resizing (like pThumb et al)
- Relies on standard Packagist packages to conduct image manipulation


Technical Note:

The Asset Manager helps maintain an ordered folder structure for your images and other assets while 
keeping database records on those file assets.  Images are NOT stored as binary data in the database,
so it is critical that the database and the filesystem be kept in sync.  Do not meddle around with the
files on the filesystem that have been put in place by the Asset Manager!  Doing so may break your site.

You are forced to use the UI so that the database "knows" about the assets you have added. The Asset
Manager won't "know" about an asset if you upload it manually (e.g. using SFTP).


Author: everett@craftsmancoding.com