/* ----------------------------------------------------------------------
 * js/ca/ca.networkvisualization.js
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2010-2013 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
var caUI = caUI || {};

(function ($) {
	caUI.initNetworkVisualization = function(options) {
		// --------------------------------------------------------------------------------
		// setup options
		var that = jQuery.extend({
			container: "caResultNetwork",
			graph: {},
			dataMap: {},
			dataMapIndex: 0,
			
			svg:	null,
			
			initialData: {},
			
			dataURL: null,
			nodeLinkURL: null,
			
			nodeColor: 'cc0000',
			nodeLabelBackgroundColor: 'rgb(244,235,230)',
			
			linkKey: null,
			linkColors: null,
			linkWeights: null,
			linkColorDefault: 'cccccc',
			linkWeightDefault: '2px'
		}, options);
		
		// --------------------------------------------------------------------------------
		// Define methods
		// --------------------------------------------------------------------------------
		that.setup = function() {
			var width = 690, height = 500;
	
			that.graph = {};

			that.svg = d3.select('#' + that.container).append('svg')
				.attr('width', '100%')
				.attr('height', '450');

			that.force = d3.layout.force()
				.gravity(.06)
				.linkDistance(function(d) { return 50 + (Math.random() * 250) + (d.index ? d.index : 0); })
				.size([width, height]);
				
			that.graph = that._processData(that.initialData);
			that._drawData(true);
		};
		
		// -----------------------------------------------------------
		that._drawData = function(isInit) {
			var json = that.graph;
			
			
			//
			// Clean up old nodes and links
			// (Note: we don't remove them, only set opacity to 0 since we may have to "resurrect" them as we navigate around)
			//
			that.svg.selectAll('.node')
				.data(json.nodes).exit().attr('opacity', 0.0);
			
			that.svg.selectAll('.link')
				.data(json.links).exit().attr('opacity', 0.0);
				
			//
			// Do force layout
			//
			that.force
				.nodes(json.nodes)
				.links(json.links)
				.start();
		
			//
			// Add new links
			//	
			var link = that.svg.selectAll('.link')
				.data(json.links)
				.enter().append('g').attr('class', 'linkContainer').append('line')
				.style('stroke', function (d) { return (that.linkColors && that.linkKey && d[that.linkKey] && that.linkColors[d[that.linkKey]]) ? that.linkColors[d[that.linkKey]] : that.linkColorDefault; })
				.style('stroke-width', function (d) { return (that.linkWeights && that.linkKey && d[that.linkKey] && that.linkWeights[d[that.linkKey]]) ? that.linkWeights[d[that.linkKey]] : that.linkWeightDefault;  })
				.attr('class', 'link');
				
			
			//
			// Add new nodes
			//
			var nodeSet = that.svg.selectAll('.node')
				.data(json.nodes)
				.enter();
				
				// Create clickable container for node
				var node = nodeSet.append('g')
					.attr('class', 'node')
					.on('click', function(d) { 
						d3.json(that.dataURL + d.id, function(error, theData) {
							var jsonData = that._processData(theData);
							that.graph = jsonData;
							that._drawData();
					})
				})
				.call(that.force.drag);
			
				if (that.nodeLinkURL) {
					node.on('dblclick', function(d) { 
						window.location = that.nodeLinkURL + d.id;
					});
				}
			
				// Add circle at node location
				node.append('circle')
					.attr('x', -8)
					.attr('y', -8)
					.attr('r', function(d) { return ((d.focus == 1) ? 14 : 6) })
					.attr('opacity', 0.8)
					.style('fill', that.nodeColor);
				
				
				// Add node text label
				node.append('text')
					.attr('dx', function(d) { return ((d.focus == 1) ? 20 : 8) })
					.attr('dy', function(d) { return ((d.focus == 1) ? -1 : -4) })
					.attr('class', 'nodeLabel')
					.style("font-size",function(d) { return ((d.focus == 1) ? "18px" : "12px") })
					.style("font-weight",function(d) { return ((d.focus == 1) ? "bold" : "normal") })
					.text(function(d) { return d.name });

				//
				// Add backing rectangles to improve legibility
				//				
				node.insert('rect', ":first-child")
					.attr('x', function(d) { return ((d.focus == 1) ? 17 : 5) })
					.attr('y', function(d) { return ((d.focus == 1) ? -21 : -18) })
					.attr('rx', 6)
					.attr('ry', 6)
					.attr('class', 'nodeLabelBackground')
					.attr('width', function(d, i) { 
						var s = node.selectAll('.nodeLabel');
						if (!s || !s[i] || !s[i][0] || !s[i][0].clientWidth) { return 100; }
						return s[i][0].clientWidth + 6;
					})
					.attr('height', function(d, i) { 
						var s = node.selectAll('.nodeLabel');
						if (!s || !s[i] || !s[i][0] || !s[i][0].clientHeight) { return 18; }
						return s[i][0].clientHeight + 6;
					})
					.attr('opacity', 0.5)
					.style('fill', that.nodeLabelBackgroundColor);
				
				// Add image for node if available	
				// node.append('image')
				// 			.attr('xlink:href', function(d) { return d.media; })
				// 			.attr('x', -8)
				// 			.attr('y', -8)
				// 			.attr('width', function(d) { return (d.focus == 1) ? 32 : 16; } )
				// 			.attr('height', function(d) { return (d.focus == 1) ? 32 : 16; });
			
			
			//
			// force opacity of all current nodes and links to 1.0
			// (This has the effect of "resurrecting" previously show and removed nodes
			//
			that.svg.selectAll('.node')
				.data(json.nodes).attr('opacity', 1.0);
				
			that.svg.selectAll('.link')
				.data(json.links).attr('opacity', 1.0);
							
			//
			// Update text labels for existing nodes
			//
			that.svg.selectAll('.nodeLabel').data(json.nodes).text(function(d) { return d.name });
			that.svg.selectAll('.nodeLabelBackground').data(json.nodes).attr('width', function(d, i) { 
						var s = that.svg.selectAll('.nodeLabel').data(json.nodes);
						return s[0][i].clientWidth + 6; 
					})
					.attr('height', function(d, i) { 
						var s = that.svg.selectAll('.nodeLabel').data(json.nodes);
						return s[0][i].clientHeight + 6;
					});
			
			//
			// Set up force function on initial layout
			//
			if (isInit) {	
				that.force.on('tick', function() {
					link.attr('x1', function(d) { return d.source.x; })
					.attr('y1', function(d) { return d.source.y; })
					.attr('x2', function(d) { return d.target.x; })
					.attr('y2', function(d) { return d.target.y; });

					node.attr('transform', function(d) { return 'translate(' + d.x + ',' + d.y + ')'; });
				});
			}
		};
		
		// -----------------------------------------------------------
		that._processData = function(data) {
			that.dataMapIndex = 0;
			that.dataMap = {};
			for(var k in data['links']) {
				var l = data['links'][k];
				if (that.dataMap[l['source_id']] == undefined) {
					that.dataMap[l['source_id']] = parseInt(that.dataMapIndex);
					that.dataMapIndex++;
				}
				data['links'][k]['source'] = that.dataMap[l['source_id']];
				 
				 
				if (that.dataMap[l['target_id']] == undefined) {
					that.dataMap[l['target_id']] = parseInt(that.dataMapIndex);
					that.dataMapIndex++;
				}
				data['links'][parseInt(k)]['target'] = parseInt(that.dataMap[l['target_id']]);
			}
			//console.log("data was ", data);
			return data;
		}
		

		// --------------------------------------------------------------------------------
		
		that.setup();
		
		return that;
	};	
})(jQuery);