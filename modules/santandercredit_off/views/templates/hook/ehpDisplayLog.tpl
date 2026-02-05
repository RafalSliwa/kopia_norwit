<div class="panel">
    <div class="row">
        <div class="col-md-12 d-print-block left-column">
            <div class="panel card mt-2" style="text-align:left;">
                <div class="card-header">
                    <h3 class="card-header-title">Log komunikacji z systemem bankowym:</h3>                
                </div>
                <div class="card-body" style="text-align:right;overflow: scroll; padding-right:6px;">
                    <table class="table" style="margin-right:6px; matgin-left:auto;">
                        <thead>
                            <tr>
                                <th class="table-head-date">request_date</th>
                                <th>success</th>
                                <th>shop_number</th>       
                                <th>id_order</th>
                                <th>pos_app_number</th> 
                                <th>application_number</th>
                                <th>application_status</th>
                                <th>app_status_chg_date</th>
                                <th>agreement_number</th>
                                <th>agreement_date</th>                                
                                <th>message</th>
                                <th>downpayment</th>
                                <th>total_price</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$log item=row}
                                <tr>
                                    <td>{$row['request_date']}</td>
                                    <td>{$row['success']}</td>
                                    <td>{$row['shop_number']}</td>
                                    <td>{$row['id_order']}</td>
                                    <td>{$row['pos_app_number']}</td>
                                    <td>{$row['application_number']}</td>
                                    <td>{$row['application_status']}</td>
                                    <td>{$row['app_status_chg_date']}</td>
                                    <td>{$row['agreement_number']}</td>
                                    <td>{$row['agreement_date']}</td>
                                    <td><textarea cols="20" rows="3">{$row['message']}</textarea>
                                    <td>{$row['downpayment']}</td>
                                    <td>{$row['total_price']}</td>                                                                       
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>