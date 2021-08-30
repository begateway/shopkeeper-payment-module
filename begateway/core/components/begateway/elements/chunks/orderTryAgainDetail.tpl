<p>Попытка оплатить заказ завершилась неуспешно.</p>
<div class="shop-cart">
    <form action="[[+redirect_url]]" method="post">
        <fieldset>
            <h3>Заказ № [[+order_id]]</h3>
            <table class="table table-hover" width="100%">
                <thead>
                <tr>
                    <th scope="col">Наименование</th>
                    <th scope="col">Параметры</th>
                    <th scope="col" width="200" class="text-center">Кол-во</th>
                    <th scope="col" width="150" class="text-end">Цена</th>
                </tr>
                </thead>
                <tbody>
                [[+purchases]]
                </tbody>
            </table>
            <div class="text-end my-2">
                Доставка: [[+delivery_name]] ([[+delivery_price:num_format]] [[+currency]])
            </div>
            <div class="text-end my-2">
                <h3>Общая сумма: <b>[[+price_total:num_format]]</b> [[+currency]]</h3>
            </div>
            <table class="table">
                <tbody>
                [[+contacts]]
                </tbody>
            </table>
            <div class="row my-3">
                <div class="col-md-12 text-end">
                    <input type="submit" name="submit" class="btn btn-primary" value="Повторить оплату">
                </div>
            </div>
        </fieldset>
    </form>
</div>
