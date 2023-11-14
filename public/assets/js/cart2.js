function add_to_cart(product_id) {
$.post( "/catalog/ajax/add_to_cart", {product_id: product_id}, update_cart()); 
setTimeout(function(){ 
var notification = new NotificationFx({
		message : '<p>Товар успешно добавлен в вашу корзину. Вы можете продолжить или <a href="/catalog/cart">перейти корзину</a>.</p>',
		layout : 'growl',
		effect : 'slide',
		type : 'notice'
	});
	
	notification.show();
	},1000);
}
function update_cart() {
	setTimeout(function(){ 
		$.post( "/catalog/ajax/update_cart", {}, on_success); 
		function on_success(data)
		{
			$('#small_cart').html(data);
		}
	},1000);
}
function remove_from_cart(product_id) {
	$("#overlay").fadeIn();
	$("#loading").fadeIn();
	$.post( "/catalog/ajax/remove_from_cart", {product_id:product_id}, update_cart_interface('2')); 
}

function update_product_count(product_id, count) {
	$("#overlay").fadeIn();
	$("#loading").fadeIn();
	$.post( "/catalog/ajax/update_product_count", {product_id:product_id, count:count}, function(data) 
		{				
			update_cart_interface('1');
		});
			
		

}
function update_cart_interface(ttype) {
	setTimeout(function(){ 
		$.post( "/catalog/ajax/cart_interface", {}, on_success); 
		function on_success(data)
		{
			$('#cart_interface').html(data);
			if (ttype == '1')
			{
			var notification = new NotificationFx({
						message : '<p>Колличество товара успешно обновлено</p>',
						layout : 'growl',
						effect : 'slide',
						type : 'notice'
					});
			}
			else
			{
				var notification = new NotificationFx({
						message : '<p>Товар удален из корзины!</p>',
						layout : 'growl',
						effect : 'slide',
						type : 'notice'				
					});
			}
			$("#overlay").fadeOut('fast'); 
			$("#loading").fadeOut('fast'); 
			update_cart();
			notification.show();	
		}
	},1000);
}
