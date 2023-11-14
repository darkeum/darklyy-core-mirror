 <tr class="cart__row border-bottom line1 cart-flex border-top">
              <td class="cart__image-wrapper cart-flex-item">
                <a href="/catalog/{%FULL_LINK%}">
                  <img class="cart__image" src="{%PREVIEW%}" alt=" {%TITLE%}">
                </a>
              </td>
              <td class="cart__meta small--text-left cart-flex-item">
                <div class="list-view-item__title">
                  <a href="/catalog/{%FULL_LINK%}">
                   {%TITLE%}
                    
                  </a>
                </div>
              
              </td>
              <td class="cart__price-wrapper cart-flex-item">
                <span class='money'>{%PRICE%}</span>

                

                <div class="cart__edit medium-up--hide">
              
                </div>
              </td>
              <td class="cart__update-wrapper cart-flex-item text-right">          
                <div class="cart__qty">             
                  <input class="cart__qty-input" type="number" id="product_count_{%CART_ID%}"  onchange="update_product_count('{%CART_ID%}', $('#product_count_{%CART_ID%}').val())" value="{%CART_COUNT%}" min="0" pattern="[0-9]*">
                </div>              
              </td>
              <td class="text-right small--hide">
                

                <div>
                  <span class='money'>{%CART_SUM%}</span>
                </div>

                
              </td>
			    <td class="text-center">
                

                <div>
                <span style="cursor: pointer;" onclick="remove_from_cart({%CART_ID%})"> <i class="fa fa-close" style="color: #fa381d; font-size: 18px;"></i></span>
                </div>

                
              </td>
            </tr>