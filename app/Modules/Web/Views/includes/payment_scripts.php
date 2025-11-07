<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: innovate
 * Date: 20/09/2023
 * Time: 12:40
 */
?>

<div class="modal fade bs-example-modal-sm paypal-button" id="upgrade_ticket" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="overflow-y: scroll !important;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card" style="border: none !important;">
                    <div class="card-body text-center" style="padding: 0 !important;">
                        <div class="card-img-top epr-purple text-center" style="width: 100%; padding: 10px;">
                            <h2 class="text-white mt-3">$<span class="ticket-price"></span></h2>
                            <h6 class="text-white mt-3">PREMIUM ACCESS TO EVERYTHING THE INNOVATE DIGITAL EXPERIENCE HAS TO OFFER PLUS SPECIAL PERKS AND DISCOUNTS.</h6>
                        </div>
                        <div id="paypal-button-container"></div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade paypal_success" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body"><!--modal body-->
                <div id="success_box" class="w-100 text-center" style="margin-top: -50px;">
                    <span style="font-size: 150px; color: #9D0F82;"><i class="fa fa-check-circle-o" aria-hidden="true"></i></span>
                    <p><strong><span class="modal_error">You have successfully purchased your ticket for the Emergence conference Innovate 2.0. You will be logged out, please proceed to login again to have access to the platform.</span> </strong></p>
                </div>
            </div><!--modal-body close-->

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade bs-example-modal-sm" id="display_price" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card" style="border: none !important;">
                    <div class="card-body text-center" style="padding: 0 !important;">
                        <div class="card-img-top epr-purple text-center" style="width: 100%; padding: 10px;">
                            <h3 class="text-white mt-3"><span class="pay-currency"></span><?php echo $ticket_price;?></h3>
                            <h6 class="text-white mt-3">PREMIUM ACCESS TO EVERYTHING THE INNOVATE DIGITAL EXPERIENCE HAS TO OFFER PLUS SPECIAL PERKS AND DISCOUNTS.</h6>
                            <div class="row mt-2">
                                <button type="button" class="btn btn-epr-pink buy-now" style="margin: auto;">Buy Now</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade coupon-box" id="upgrade_ticket_flw" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="margin-top: 100px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row" style="">
                    <div class="col-md-6 border-right">
                        <form id="buyTicketsForm" method="post" class="text-center" action="" style="margin: auto;">
                            <div class="form-group">
                                <label for="no_tickets" class="epr-text-pink">Choose Number of Tickets</label>
                                <select class="form-control epr-text-purple" id="no_tickets" name="no_tickets" required style="border: solid 1px #9D0F82 !important; outline: #9D0F82 !important; color: #D8198E !important;">
                                    <option value="">Choose No. of tickets</option>

                                    <?php
                                    for($i=1; $i<=5; $i++){
                                        ?>
                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <p class="mb-5 mt-1 epr-text-pink" style="font-size: 10px;">You can purchase bulk tickets by selecting the required number above. To assign the tickets, ask each recipient to create a profile on the event platform and send their respective names to <a href="mailto:registration@eprglobal.com">registration@eprglobal.com</a> so they can be upgraded.
                            </p>


                            <div class="form-group mb-5">
                                <label for="coupon_code" class="font-10  epr-text-pink"><strong>If you've got a coupon code, enter it below</strong></label>
                                <br><br>
                                <input type="text" class="epr-text-purple" id="coupon_code" name="coupon_code" placeholder="Enter coupon code." style="height: 30px;">
                                <button type="button" class="btn btn-epr-pink" id="applyCoupon" style="width: auto;">Apply Coupon</button><br>
                                <span class="epr-text-purple good-coupon mt-1" style="display: none;">Valid coupon</span>
                                <span class="epr-text-pink bad-coupon mt-1" style="display: none;">Invalid coupon</span>
                                <input type="hidden" id="good_coupon" name="good_coupon">
                                <input type="hidden" id="discount" name="discount">
                                <input type="hidden" id="currency" name="currency" value="<?php echo $currency;?>">
                                <input type="hidden" id="amount_to_pay" name="amount_to_pay">
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="row mb-5">
                            <?php
                            if($country == "Nigeria"){
                                ?>
                                <h5 class="epr-text-pink" style="font-size: 15px; margin: auto !important;">You are paying <strong>NGN<span class="ticket-price"><?php echo $ticket_price?> </span></strong></h5>
                                <?php
                            }
                            elseif ($country == "Kenya"){
                                ?>
                                <h5 class="epr-text-pink" style="font-size: 15px; margin: auto !important;">You are paying <strong>Ksh<span class="ticket-price"><?php echo $ticket_price?> </span></strong></h5>
                                <?php
                            }
                            else{
                                ?>
                                <h5 class="epr-text-pink" style="font-size: 15px; margin: auto !important;">You are paying <strong>$<span class="ticket-price"><?php echo $ticket_price?> </span></strong></h5>
                                <?php
                            }
                            ?>
                        </div>

                        <div class="form-group mt-5 text-center">
                            <button type="button" class="btn btn-epr-purple buy-button" onclick="buy_ticket('<?php echo $currency;?>')" style="margin: auto !important;">Buy Tickets</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var attendee_id = "<?php echo $this->session->attendee_id;?>";
    var useremail = "<?php echo $this->session->useremail;?>";
    var telephone = "<?php echo $this->session->telephone;?>";
    var fullname = "<?php echo $this->session->firstname." ".$this->session->lastname;?>";

    let ticket_price = "<?php echo $ticket_price;?>";
    let currency;
    let country;
    let before_discount_price = parseFloat(<?php echo $ticket_price;?>);
    let multi_ticket_price;


    $(document).ready(function(){
        //$('#video_button').click();

        $('#no_tickets').on('change', function () {
            //alert();
            $('#coupon_code').val('');
            $('.bad-coupon').hide();
            $('.good-coupon').hide();
            let x = $('#no_tickets').val()*before_discount_price;
            multi_ticket_price =x;
            ticket_price = x;
            //alert(x);
            $('.ticket-price').html(x);
            $('#amount_to_pay').val(x);
        });

        $('.upgrade-link').on('click', function (e) {
            //alert('efefe');
            e.preventDefault();
            country = $(this).data('country');
            currency = $(this).data('currency');
            //alert(currency);
            if(currency === "USD"){
                $('#display_price').modal('toggle');
                $('.pay-currency').html(currency);
                //$('#upgrade_ticket').modal('toggle');
            }
            else{
                $('#display_price').modal('toggle');
                $('.pay-currency').html(currency);
            }
        });

        $('.buy-now').on('click', function () {
            $('#display_price').modal('toggle');
            $('#upgrade_ticket_flw').modal('toggle');
        });

        $('#applyCoupon').on('click', function () {
            //alert('haha');

            let coupon_code = $('#coupon_code').val();
            let selectedValue = $("#no_tickets option:selected").val();


            let before_discount_price = parseFloat(ticket_price);

            let discounted_ticket_price;
            $.ajax({
                type: "GET",
                url: "<?php echo base_url('registration/registration/validate_coupon');?>",
                data: "coupon_code="+coupon_code,
                success: function (data) {
                    // alert(data);
                    console.log(data);
                    if(data === "false"){
                        $('.bad-coupon').show();
                        $('.good-coupon').hide();
                    }
                    else{
                        const obj = JSON.parse(data);
                        console.log(obj);

                        if(obj.no_tickets > 0){
                            if(obj.no_tickets === selectedValue){
                                if(obj.currency === currency){
                                    $('.bad-coupon').hide();
                                    $('.good-coupon').show();
                                    $('#good_coupon').val(coupon_code);
                                    $('#discount').val(obj.discount);

                                    discounted_ticket_price = multi_ticket_price - obj.discount;
                                    ticket_price = discounted_ticket_price;
                                    // alert(discounted_ticket_price);
                                    $('.ticket-price').html(discounted_ticket_price);
                                    $('#amount_to_pay').val(discounted_ticket_price);
                                }
                                else{
                                    alert("This coupon does not apply to your country." );
                                }
                            }
                            else{
                                $('.bad-coupon').show();
                                $('.good-coupon').hide();
                                alert("This coupon applies only if you are buying "+obj.no_tickets+" tickets." );
                            }
                        }
                        else{
                            $('.bad-coupon').hide();
                            $('.good-coupon').show();
                            $('#good_coupon').val(coupon_code);
                            $('#discount').val(obj.discount);
                            discounted_ticket_price = before_discount_price - obj.discount;
                            //discounted_ticket_price = before_discount_price - obj.discount;
                            //alert(discounted_ticket_price);
                            $('.ticket-price').html(discounted_ticket_price);
                        }
                    }
                }
                //document.location = data;
            });

        });

    });

    function buy_ticket() {
        let amount_to_pay = $('#amount_to_pay').val();
        $('.ticket-price').html(amount_to_pay);

        if(currency === "USD"){
            ticket_price_ = amount_to_pay;
            //alert(ticket_price_);
            $('.coupon-box').modal('hide');
            $('.paypal-button').modal('toggle');
        }
        else{
            $('.coupon-box').modal('toggle');
            makePayment(amount_to_pay, currency);
        }
    }

</script>

<script src="https://checkout.flutterwave.com/v3.js"></script>

<script>
    function makePayment(amount, currency) {
        let transref = "epr-<?php echo date('y').transaction_ref();?>";
        FlutterwaveCheckout({
            public_key: "FLWPUBK-81b14e73cc85cb37c3470031779b303d-X",
            tx_ref: transref,
            amount: amount,
            currency: currency,
            payment_options: "card, mobilemoneyghana, ussd",
            redirect_url: "<?php echo base_url('attendees/f/verifyPayment/');?>"+transref+"/"+amount+"/"+currency,
            meta: {
                consumer_id: attendee_id,
                consumer_mac: attendee_id,
            },
            customer: {
                email: useremail,
                phone_number: telephone,
                name: fullname,
            },
            customizations: {
                title: "EPR Global",
                description: "Payment for EPR Conference (INNOVATE) 2022",
                logo: "https://portal.eprglobal.com/assets/images/logo-dark.png",
            },
        });
    }
</script>

<script src="https://www.paypal.com/sdk/js?client-id=AeWV6ycQeDqsTGce3vNopqs7fJ288Aq0tlYSmN8sFkmu5ptiGakvZBhFvY8Ap-TbJWIG0xFqQ6UbZ2p1&currency=USD&intent=capture&enable-funding=venmo" data-sdk-integration-source="integrationbuilder"></script>

<script>
    const fundingSources = [
        paypal.FUNDING.PAYPAL,
        paypal.FUNDING.CARD
    ]

    for (const fundingSource of fundingSources) {
        const paypalButtonsComponent = paypal.Buttons({
            fundingSource: fundingSource,

            // optional styling for buttons
            // https://developer.paypal.com/docs/checkout/standard/customize/buttons-style-guide/
            style: {
                shape: 'rect',
                height: 40,
            },

            // set up the transaction
            createOrder: (data, actions) => {
                // pass in any options from the v2 orders create call:
                // https://developer.paypal.com/api/orders/v2/#orders-create-request-body
                const createOrderPayload = {
                    purchase_units: [
                        {
                            amount: {
                                value: ticket_price,
                            },
                        },
                    ],
                }

                return actions.order.create(createOrderPayload)
            },

            // finalize the transaction
            onApprove: (data, actions) => {
                const captureOrderHandler = (details) => {
                    const payerName = details.payer.name.given_name;
                    console.log('Transaction completed!');
                    //console.log(details);
                    let attendee_id = '<?php echo $this->session->attendee_id;?>';
                    let transid = details.id;
                    let ticket_price = '<?php echo $ticket_price;?>';
                    const payload = JSON.stringify(details);
                    let created_at = details.update_time;

                    $.ajax({
                        type: "GET",
                        url: "<?php echo base_url('attendees/register/update_payment');?>",
                        data: "attendee_id="+attendee_id+"&transid="+transid+"&amount="+ticket_price+"&payload="+payload+"&created_at="+created_at,
                        success: function (data) {
                            $('.paypal').modal('toggle');
                            $('.paypal_success').modal('toggle');
                            $('.paypal_success').on('hidden.bs.modal', function () {
                                location.href = "<?php echo base_url('attendee/logout');?>";
                            });

                        }
                        //document.location = data;
                    });

                }

                return actions.order.capture().then(captureOrderHandler);
            },

            // handle unrecoverable errors
            onError: (err) => {
                console.error(
                    'An error prevented the buyer from checking out with PayPal',
                )
            },
        })

        if (paypalButtonsComponent.isEligible()) {
            paypalButtonsComponent
                .render('#paypal-button-container')
                .catch((err) => {
                    console.error('PayPal Buttons failed to render')
                })
        } else {
            console.log('The funding source is ineligible')
        }
    }
</script>
