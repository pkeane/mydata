var Dase = {};

$(document).ready(function() {
	Dase.initDelete('topMenu');
	Dase.initToggle('target');
	Dase.initToggle('email');
	Dase.initSortable('target');
	Dase.initUserPrivs();
	Dase.initFormDelete();
	Dase.initDataTable();
});

Dase.activateEditing = function() {
	$('table').find('a.edit').click(function() {
		var td = $(this).parents('td');
		var type = $(this).parents('td').attr('class');
		var url = $(this).attr('href');
		$.get(url, function(data) {
			td.html(data);
			td.find('form[method="post"]').submit(function() {
				var row = $(this).parents('tr');
				var row_url = row.find('td.control a').attr('href')+'/rowdata';
				var post_url = $(this).attr('action');
				$.post(post_url,$(this).serialize(),function(res) {
					$.get(row_url,function(rowdata) {
						row.html(rowdata);
						Dase.activateEditing();
					});
				});
				return false;
			});
			/* cancel form: */
			td.find('form[method="get"]').submit(function() {
				var row = $(this).parents('tr');
				var row_url = row.find('td.control a').attr('href')+'/rowdata';
				$.get(row_url,function(rowdata) {
					row.html(rowdata);
					Dase.activateEditing();
				});
				return false;
			});
			/* delete form: */
			td.find('form[method="delete"]').submit(function() {
				var row = $(this).parents('tr');
				var row_url = row.find('td.control a').attr('href')+'/rowdata';
				var delete_url = $(this).attr('action');
				if (confirm('delete this value?')) { 
					$.ajax({
						type: "DELETE",
						url: delete_url,
						data: "",
						success: function(msg){
							$.get(row_url,function(rowdata) {
								row.html(rowdata);
								Dase.activateEditing();
							});
						}
					});
				} else {
				}
				return false;
			});
		});
		return false;
	});
	$('table').find('a.add').click(function() {
		var td = $(this).parents('td');
		var type = $(this).parents('td').attr('class');
		var url = $(this).attr('href');
		$.get(url, function(data) {
			td.html(data);
			td.find('form[method="post"]').submit(function() {
				var row = $(this).parents('tr');
				var row_url = row.find('td.control a').attr('href')+'/rowdata';
				var post_url = $(this).attr('action');
				$.post(post_url,$(this).serialize(),function(res) {
					$.get(row_url,function(rowdata) {
						row.html(rowdata);
						Dase.activateEditing();
					});
				});
				return false;
			});
			/* cancel form: */
			td.find('form[method="get"]').submit(function() {
				var row = $(this).parents('tr');
				var row_url = row.find('td.control a').attr('href')+'/rowdata';
				$.get(row_url,function(rowdata) {
					row.html(rowdata);
					Dase.activateEditing();
				});
				return false;
			});
		});
		return false;
	});
	$('table').find('a.delete').click(function() {
		var row = $(this).parents('tr');
		var row_url = row.find('td.control a').attr('href')+'/rowdata';
		if (confirm('are you sure?')) {
			var del_o = {
				'url': $(this).attr('href'),
				'type':'DELETE',
				'success': function(resp) {
					$.get(row_url,function(rowdata) {
						row.html(rowdata);
						Dase.activateEditing();
					});
				},
				'error': function() {
					alert('sorry, cannot delete');
					$.get(row_url,function(rowdata) {
						row.html(rowdata);
						Dase.activateEditing();
					});
				}
			};
			$.ajax(del_o);
		}
		return false;
	});
};

Dase.initDataTable = function() {
	Dase.activateEditing();
	if ($('table').hasClass('editable')) {
		$('table').find('a.edit').hide();
		$('table').find('a.add').hide();
		$('table').find('a.delete').hide();
		$('#edit_off').hide();
	};
	$('#edit_on').click(function() {
		$('table').find('a.edit').show();
		$('table').find('a.add').show();
		$('table').find('a.delete').show();
		$('#edit_off').show();
		$(this).hide();
		return false;
	});
	$('#edit_off').click(function() {
		$('table').find('a.edit').hide();
		$('table').find('a.add').hide();
		$('table').find('a.delete').hide();
		$('#edit_on').show();
		$(this).hide();
		return false;
	});
};

Dase.initToggle = function(id) {
	$('#'+id).find('a[class="toggle"]').click(function() {
		var id = $(this).attr('id');
		var tar = id.replace('toggle','target');
		$('#'+tar).toggle();
		return false;
	});	
};

Dase.initFormDelete = function() {
	$("form[method='delete']").submit(function() {
		if (confirm('are you sure?')) {
			var del_o = {
				'url': $(this).attr('action'),
				'type':'DELETE',
				'success': function() {
					location.reload();
				},
				'error': function() {
					alert('sorry, cannot delete');
				}
			};
			$.ajax(del_o);
		}
		return false;
	});
};

Dase.initDelete = function(id) {
	$('#'+id).find("a[class='delete']").click(function() {
		if (confirm('are you sure?')) {
			var del_o = {
				'url': $(this).attr('href'),
				'type':'DELETE',
				'success': function(resp) {
					if (resp.location) {
						location.href = resp.location;
					} else {
						location.reload();
					}
				},
				'error': function() {
					alert('sorry, cannot delete');
				}
			};
			$.ajax(del_o);
		}
		return false;
	});
};

Dase.initSortable = function(id) {
	$('#'+id).sortable({ 
		cursor: 'crosshair',
		opacity: 0.6,
		revert: true, 
		start: function(event,ui) {
			ui.item.addClass('highlight');
		},	
		stop: function(event,ui) {
			$('#proceed-button').addClass('hide');
			$('#unsaved-changes').removeClass('hide');
			$('#'+id).find("li").each(function(index){
				$(this).find('span.key').text(index+1);
			});	
			ui.item.removeClass('highlight');
		}	
	});
};
 
Dase.initUserPrivs = function() {
	$('#user_privs').find('a').click( function() {
		var method = $(this).attr('class');
		var url = $(this).attr('href');
			var _o = {
				'url': url,
				'type':method,
				'success': function(resp) {
					alert(resp);
					location.reload();
				},
				'error': function() {
					alert('sorry, there was a problem');
				}
			};
			$.ajax(_o);
		return false;
	});
};

