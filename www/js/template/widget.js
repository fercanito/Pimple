$p.Widget = function(template,container) {
	var newDate = new Date;
	this.guid = newDate.getTime();
	$p.Widget.windows[this.guid] = this;
	this.template = template;
	this.container = $(container);
	this.data = {};
};

$p.Widget.windows = {};

$p.getWidget = function(g) {
	return $p.Widget.windows[g];
}

$p.Widget.prototype = {
	setData: function(data) {
		this.data = data;
		if(this.data.rows != null) {
			this.rows = this.data.rows;
			/* Preset variables */
			this.data.currentPageIndex = (this.data.currentPageIndex!=null) ? this.data.currentPageIndex : 0;
			this.data.totalRows = (this.data.totalRows!=null) ? this.data.totalRows : this.data.rows.length;
			this.data.rowsPerPage = (this.data.rowsPerPage==null) ? this.data.totalRows : this.data.rowsPerPage;
			this.data.totalPages = Math.ceil(this.data.origTotalRows/this.data.rowsPerPage);
		}
	},
	setJSON: function(url) {
		var c = this;
		$.ajax({
		    type: 'GET',
		    url: url,
		    dataType: 'json',
		    success: function(d) {
		    	c.setData(d);
		    },
		    async: false
		});
	},
	render: function(fn) {
		this.container.html(this.template(this.data, this.guid));
		if(fn!=null) {
			fn();
		}
	},
	setPaging: function(rowsPerPage) {
		this.data.totalPages = Math.ceil(this.data.origTotalRows/rowsPerPage);
		this.data.rowsPerPage = rowsPerPage;
		this.data.totalRows = (this.data.origTotalRows > rowsPerPage) ? rowsPerPage : this.data.origTotalRows;
		this.data.currentPageIndex = 0;
	},
	getData: function() {
		return this.data;		
	},
	getPage: function(pageIndex, fn) {
		this.setPage(pageIndex);
		this.render(fn);
		return false;
	},
	setPage: function(pageIndex) {
		var start = this.data.totalRows*pageIndex;
		var end = ((start+this.data.rowsPerPage) > this.data.origTotalRows) ? this.data.origTotalRows : (start+this.data.rowsPerPage);
		var newRows = new Array();
		for(var i=start;i<end;i++) {
			newRows.push(this.rows[i]);
		}
		this.data.totalRows = newRows.length;
		this.data.currentPageIndex = parseInt(pageIndex);
		this.data.rows = newRows;
	},
	setSort: function(field, order) {
		this.data.sortOrder = order.toLowerCase();
		this.rows.sort(function(x,y) {
			var first = x[field];
			if(!isNaN(parseInt(first))) {
				if(order.toLowerCase() == 'desc') {
					return x[field] - y[field];
				}
				return y[field] - x[field];
			} else {
				if(x[field]==y[field]){return 1;}
	            if(order.toLowerCase() == 'asc'){
	                if(x[field]<y[field]){return -1;}
	            }else{
	                if(x[field]>y[field]){return -1;}
	            }
	            return 1;
			}
		});
		this.getPage(this.data.currentPageIndex);
	}
}