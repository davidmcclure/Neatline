
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2 cc=80; */

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2012 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

Neatline.module('Map.Layers.OpenStreetMap', function(OpenStreetMap) {


  OpenStreetMap.Controller = Neatline.Shared.Controller.extend({


    slug: 'MAP:LAYERS',

    requests: ['OpenStreetMap'],


    /**
     * Construct an OpenStreetMap layer.
     *
     * @param {Object} json: The layer definition.
     * @return {OpenLayers.Layer.OSM}: The OSM layer.
     */
    OpenStreetMap: function(json) {
      return new OpenLayers.Layer.OSM(json.title);
    }


  });


});
