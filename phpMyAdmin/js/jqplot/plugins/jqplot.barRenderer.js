(function(i){function m(d,c,b,e,a){var g=d-1,f=a==="x"?0:1;if(d>0){d=e.series[g]._plotData[c][f];c=b*d<0?m(g,c,b,e,a):e.series[g].gridData[c][f]}else c=f===0?e.series[d]._xaxis.series_u2p(0):e.series[d]._yaxis.series_u2p(0);return c}function r(){for(var d=0;d<this.series.length;d++)if(this.series[d].renderer.constructor==i.jqplot.BarRenderer)if(this.series[d].highlightMouseOver)this.series[d].highlightMouseDown=false}function s(){if(this.plugins.barRenderer&&this.plugins.barRenderer.highlightCanvas){this.plugins.barRenderer.highlightCanvas.resetCanvas();
this.plugins.barRenderer.highlightCanvas=null}this.plugins.barRenderer={highlightedSeriesIndex:null};this.plugins.barRenderer.highlightCanvas=new i.jqplot.GenericCanvas;this.eventCanvas._elem.before(this.plugins.barRenderer.highlightCanvas.createElement(this._gridPadding,"jqplot-barRenderer-highlight-canvas",this._plotDimensions,this));this.plugins.barRenderer.highlightCanvas.setContext();this.eventCanvas._elem.bind("mouseleave",{plot:this},function(d){n(d.data.plot)})}function p(d,c,b,e){var a=d.series[c],
g=d.plugins.barRenderer.highlightCanvas;g._ctx.clearRect(0,0,g._ctx.canvas.width,g._ctx.canvas.height);a._highlightedPoint=b;d.plugins.barRenderer.highlightedSeriesIndex=c;a.renderer.shapeRenderer.draw(g._ctx,e,{fillStyle:a.highlightColors[b]})}function n(d){var c=d.plugins.barRenderer.highlightCanvas;c._ctx.clearRect(0,0,c._ctx.canvas.width,c._ctx.canvas.height);for(c=0;c<d.series.length;c++)d.series[c]._highlightedPoint=null;d.plugins.barRenderer.highlightedSeriesIndex=null;d.target.trigger("jqplotDataUnhighlight")}
function t(d,c,b,e,a){if(e){c=[e.seriesIndex,e.pointIndex,e.data];b=jQuery.Event("jqplotDataMouseOver");b.pageX=d.pageX;b.pageY=d.pageY;a.target.trigger(b,c);if(a.series[c[0]].highlightMouseOver&&!(c[0]==a.plugins.barRenderer.highlightedSeriesIndex&&c[1]==a.series[c[0]]._highlightedPoint)){b=jQuery.Event("jqplotDataHighlight");b.which=d.which;b.pageX=d.pageX;b.pageY=d.pageY;a.target.trigger(b,c);p(a,e.seriesIndex,e.pointIndex,e.points)}}else e==null&&n(a)}function u(d,c,b,e,a){if(e){c=[e.seriesIndex,
e.pointIndex,e.data];if(a.series[c[0]].highlightMouseDown&&!(c[0]==a.plugins.barRenderer.highlightedSeriesIndex&&c[1]==a.series[c[0]]._highlightedPoint)){b=jQuery.Event("jqplotDataHighlight");b.which=d.which;b.pageX=d.pageX;b.pageY=d.pageY;a.target.trigger(b,c);p(a,e.seriesIndex,e.pointIndex,e.points)}}else e==null&&n(a)}function v(d,c,b,e,a){d=a.plugins.barRenderer.highlightedSeriesIndex;d!=null&&a.series[d].highlightMouseDown&&n(a)}function w(d,c,b,e,a){if(e){c=[e.seriesIndex,e.pointIndex,e.data];
b=jQuery.Event("jqplotDataClick");b.which=d.which;b.pageX=d.pageX;b.pageY=d.pageY;a.target.trigger(b,c)}}function x(d,c,b,e,a){if(e){c=[e.seriesIndex,e.pointIndex,e.data];b=a.plugins.barRenderer.highlightedSeriesIndex;b!=null&&a.series[b].highlightMouseDown&&n(a);b=jQuery.Event("jqplotDataRightClick");b.which=d.which;b.pageX=d.pageX;b.pageY=d.pageY;a.target.trigger(b,c)}}i.jqplot.BarRenderer=function(){i.jqplot.LineRenderer.call(this)};i.jqplot.BarRenderer.prototype=new i.jqplot.LineRenderer;i.jqplot.BarRenderer.prototype.constructor=
i.jqplot.BarRenderer;i.jqplot.BarRenderer.prototype.init=function(d,c){this.barPadding=8;this.barMargin=10;this.barDirection="vertical";this.barWidth=null;this.shadowOffset=2;this.shadowDepth=5;this.shadowAlpha=0.08;this.waterfall=false;this.groups=1;this.varyBarColor=false;this.highlightMouseOver=true;this.highlightMouseDown=false;this.highlightColors=[];this.transposedData=true;this.renderer.animation={show:false,direction:"down",speed:3E3,_supported:true};this._type="bar";if(d.highlightMouseDown&&
d.highlightMouseOver==null)d.highlightMouseOver=false;i.extend(true,this,d);i.extend(true,this.renderer,d);this.fill=true;if(this.barDirection==="horizontal"&&this.rendererOptions.animation&&this.rendererOptions.animation.direction==null)this.renderer.animation.direction="left";if(this.waterfall){this.fillToZero=false;this.disableStack=true}if(this.barDirection=="vertical"){this._primaryAxis="_xaxis";this.fillAxis=this._stackAxis="y"}else{this._primaryAxis="_yaxis";this.fillAxis=this._stackAxis="x"}this._plotSeriesInfo=
this._highlightedPoint=null;this._dataColors=[];this._barPoints=[];this.renderer.shapeRenderer.init({lineJoin:"miter",lineCap:"round",fill:true,isarc:false,strokeStyle:this.color,fillStyle:this.color,closePath:this.fill});this.renderer.shadowRenderer.init({lineJoin:"miter",lineCap:"round",fill:true,isarc:false,angle:this.shadowAngle,offset:this.shadowOffset,alpha:this.shadowAlpha,depth:this.shadowDepth,closePath:this.fill});c.postInitHooks.addOnce(r);c.postDrawHooks.addOnce(s);c.eventListenerHooks.addOnce("jqplotMouseMove",
t);c.eventListenerHooks.addOnce("jqplotMouseDown",u);c.eventListenerHooks.addOnce("jqplotMouseUp",v);c.eventListenerHooks.addOnce("jqplotClick",w);c.eventListenerHooks.addOnce("jqplotRightClick",x)};i.jqplot.preSeriesInitHooks.push(function(){if(this.rendererOptions.barDirection=="horizontal"){this._stackAxis="x";this._primaryAxis="_yaxis"}if(this.rendererOptions.waterfall==true){this._data=i.extend(true,[],this.data);for(var d=0,c=!this.rendererOptions.barDirection||this.rendererOptions.barDirection===
"vertical"||this.transposedData===false?1:0,b=0;b<this.data.length;b++){d+=this.data[b][c];if(b>0)this.data[b][c]+=this.data[b-1][c]}this.data[this.data.length]=c==1?[this.data.length+1,d]:[d,this.data.length+1];this._data[this._data.length]=c==1?[this._data.length+1,d]:[d,this._data.length+1]}if(this.rendererOptions.groups>1){this.breakOnNull=true;d=this.data.length;c=parseInt(d/this.rendererOptions.groups,10);var e=0;for(b=c;b<d;b+=c){this.data.splice(b+e,0,[null,null]);e++}for(b=0;b<this.data.length;b++)if(this._primaryAxis==
"_xaxis")this.data[b][0]=b+1;else this.data[b][1]=b+1}});i.jqplot.BarRenderer.prototype.calcSeriesNumbers=function(){for(var d=0,c=0,b=this[this._primaryAxis],e,a,g=0;g<b._series.length;g++){e=b._series[g];if(e===this)a=g;if(e.renderer.constructor==i.jqplot.BarRenderer){d+=e.data.length;c+=1}}return[d,c,a]};i.jqplot.BarRenderer.prototype.setBarWidth=function(){var d=0,c=0,b=this[this._primaryAxis];c=this._plotSeriesInfo=this.renderer.calcSeriesNumbers.call(this);d=c[0];c=c[1];var e=(b.numberTicks-
1)/2;this.barWidth=b.name=="xaxis"||b.name=="x2axis"?this._stack?(b._offsets.max-b._offsets.min)/d*c-this.barMargin:((b._offsets.max-b._offsets.min)/e-this.barPadding*(c-1)-this.barMargin*2)/c:this._stack?(b._offsets.min-b._offsets.max)/d*c-this.barMargin:((b._offsets.min-b._offsets.max)/e-this.barPadding*(c-1)-this.barMargin*2)/c;return[d,c]};i.jqplot.BarRenderer.prototype.draw=function(d,c,b,e){var a;b=i.extend({},b);var g=b.shadow!=undefined?b.shadow:this.shadow;a=b.showLine!=undefined?b.showLine:
this.showLine;this._dataColors=[];this._barPoints=[];this.barWidth==null&&this.renderer.setBarWidth.call(this);var f=this._plotSeriesInfo=this.renderer.calcSeriesNumbers.call(this),j=f[1],l=f[2];f=[];this._barNudge=this._stack?0:(-Math.abs(j/2-0.5)+l)*(this.barWidth+this.barPadding);if(a){j=new i.jqplot.ColorGenerator(this.negativeSeriesColors);l=new i.jqplot.ColorGenerator(this.seriesColors);var k=j.get(this.index);if(!this.useNegativeColors)k=b.fillStyle;var q=b.fillStyle,h,o;if(this.barDirection==
"vertical")for(a=0;a<c.length;a++){if(!(!this._stack&&this.data[a][1]==null)){f=[];h=c[a][0]+this._barNudge;o=this._stack&&this._prevGridData.length?m(this.index,a,this._plotData[a][1],e,"y"):this.fillToZero?this._yaxis.series_u2p(0):this.waterfall&&a>0&&a<this.gridData.length-1?this.gridData[a-1][1]:this.waterfall&&a==0&&a<this.gridData.length-1?this._yaxis.min<=0&&this._yaxis.max>=0?this._yaxis.series_u2p(0):this._yaxis.min>0?d.canvas.height:0:this.waterfall&&a==this.gridData.length-1?this._yaxis.min<=
0&&this._yaxis.max>=0?this._yaxis.series_u2p(0):this._yaxis.min>0?d.canvas.height:0:d.canvas.height;b.fillStyle=this.fillToZero&&this._plotData[a][1]<0||this.waterfall&&this._data[a][1]<0?this.varyBarColor&&!this._stack?this.useNegativeColors?j.next():l.next():k:this.varyBarColor&&!this._stack?l.next():q;if(!this.fillToZero||this._plotData[a][1]>=0){f.push([h-this.barWidth/2,o]);f.push([h-this.barWidth/2,c[a][1]]);f.push([h+this.barWidth/2,c[a][1]]);f.push([h+this.barWidth/2,o])}else{f.push([h-this.barWidth/
2,c[a][1]]);f.push([h-this.barWidth/2,o]);f.push([h+this.barWidth/2,o]);f.push([h+this.barWidth/2,c[a][1]])}this._barPoints.push(f);if(g&&!this._stack){h=i.extend(true,{},b);delete h.fillStyle;this.renderer.shadowRenderer.draw(d,f,h)}h=b.fillStyle||this.color;this._dataColors.push(h);this.renderer.shapeRenderer.draw(d,f,b)}}else if(this.barDirection=="horizontal")for(a=0;a<c.length;a++)if(this.data[a][0]!=null){f=[];h=c[a][1]-this._barNudge;k=this._stack&&this._prevGridData.length?m(this.index,a,
this._plotData[a][0],e,"x"):this.fillToZero?this._xaxis.series_u2p(0):this.waterfall&&a>0&&a<this.gridData.length-1?this.gridData[a-1][1]:this.waterfall&&a==0&&a<this.gridData.length-1?this._xaxis.min<=0&&this._xaxis.max>=0?this._xaxis.series_u2p(0):this._xaxis.min>0?0:d.canvas.width:this.waterfall&&a==this.gridData.length-1?this._xaxis.min<=0&&this._xaxis.max>=0?this._xaxis.series_u2p(0):this._xaxis.min>0?0:d.canvas.width:0;if(this.fillToZero&&this._plotData[a][1]<0||this.waterfall&&this._data[a][1]<
0){if(this.varyBarColor&&!this._stack)b.fillStyle=this.useNegativeColors?j.next():l.next()}else b.fillStyle=this.varyBarColor&&!this._stack?l.next():q;if(!this.fillToZero||this._plotData[a][0]>=0){f.push([k,h+this.barWidth/2]);f.push([k,h-this.barWidth/2]);f.push([c[a][0],h-this.barWidth/2]);f.push([c[a][0],h+this.barWidth/2])}else{f.push([c[a][0],h+this.barWidth/2]);f.push([c[a][0],h-this.barWidth/2]);f.push([k,h-this.barWidth/2]);f.push([k,h+this.barWidth/2])}this._barPoints.push(f);if(g&&!this._stack){h=
i.extend(true,{},b);delete h.fillStyle;this.renderer.shadowRenderer.draw(d,f,h)}h=b.fillStyle||this.color;this._dataColors.push(h);this.renderer.shapeRenderer.draw(d,f,b)}}if(this.highlightColors.length==0)this.highlightColors=i.jqplot.computeHighlightColors(this._dataColors);else if(typeof this.highlightColors=="string"){f=this.highlightColors;this.highlightColors=[];for(a=0;a<this._dataColors.length;a++)this.highlightColors.push(f)}};i.jqplot.BarRenderer.prototype.drawShadow=function(d,c,b,e){var a;
b=b!=undefined?b:{};a=b.showLine!=undefined?b.showLine:this.showLine;var g,f;if(this._stack&&this.shadow){this.barWidth==null&&this.renderer.setBarWidth.call(this);f=this._plotSeriesInfo=this.renderer.calcSeriesNumbers.call(this);g=f[1];f=f[2];this._barNudge=this._stack?0:(-Math.abs(g/2-0.5)+f)*(this.barWidth+this.barPadding);if(a)if(this.barDirection=="vertical")for(a=0;a<c.length;a++){if(this.data[a][1]!=null){g=[];f=c[a][0]+this._barNudge;var j;j=this._stack&&this._prevGridData.length?m(this.index,
a,this._plotData[a][1],e,"y"):this.fillToZero?this._yaxis.series_u2p(0):d.canvas.height;g.push([f-this.barWidth/2,j]);g.push([f-this.barWidth/2,c[a][1]]);g.push([f+this.barWidth/2,c[a][1]]);g.push([f+this.barWidth/2,j]);this.renderer.shadowRenderer.draw(d,g,b)}}else if(this.barDirection=="horizontal")for(a=0;a<c.length;a++)if(this.data[a][0]!=null){g=[];f=c[a][1]-this._barNudge;j=this._stack&&this._prevGridData.length?m(this.index,a,this._plotData[a][0],e,"x"):this.fillToZero?this._xaxis.series_u2p(0):
0;g.push([j,f+this.barWidth/2]);g.push([c[a][0],f+this.barWidth/2]);g.push([c[a][0],f-this.barWidth/2]);g.push([j,f-this.barWidth/2]);this.renderer.shadowRenderer.draw(d,g,b)}}}})(jQuery);