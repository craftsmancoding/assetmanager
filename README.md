Asset Manager
=============

Asset Manager Media Library for MODX Revolution

This package adds asset management functionality to MODX Revolution so you can easily upload and edit images and other assets
and attach them to any MODX page.  Because it stores all asset data in a custom database table, you can easily search for assets
by complex criteria or manipulate the search queries for custom reporting.

The end result of using the Asset Manager can be similar to adding multiple image Template Variables to a template (e.g.
using MIGX), but the user interface and backend data model is cleaner.


## Features:

- Supports complex search criteria to find assets 
- Galleries of Images
- Drag and drop images to upload them using Dropzone
- Crop and resize images via the manager UI using jCrop
- Output filters for easy image resizing (like pThumb, but better), including scale-to-width, scale-to-height, thumbnails and cropping.
- Relies on standard Packagist packages to conduct image manipulation

![The Asset Manager in Action](https://raw.githubusercontent.com/wiki/craftsmancoding/assetmanager/images/asset-manager-tab-w-images.jpg "The Asset Manager in Action")



## Technical Note:

The Asset Manager helps maintain an ordered folder structure for your images and other assets while 
keeping database records on those file assets.  Images are not stored as binary data in the database,
so it is critical that the database and the filesystem be kept in sync.  Do not meddle around with the
files on the filesystem that have been put in place by the Asset Manager!  Doing so may break the URLs 
to your images.

You are forced to use the UI to upload assets so that the database "knows" about the assets you have added. The Asset
Manager won't "know" about an asset if you upload it manually (e.g. using SFTP).


Author: everett@craftsmancoding.com