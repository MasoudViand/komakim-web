</div>
<div class="contact" id="cnf">
    <div class="container">
        <div class="tit">تماس با ما</div>
        <div class="brd"></div>
		<div class="text-center text-info">
			تهران، خیابان الوند، خیابان 35،

پلاک 12، طبقه چهارم
		</div>
		<div class="text-center text-info">
		info@komakim.com
		</div>
		<div class="text-center text-info">
			02154585		
		</div>
		
		<div class="clearfix"></div><br><br>
        <div class="stit">شما می توانید از طریق فرم زیر با ما در تماس باشید</div>

        <div class="clearfix"></div>
        <form >

            {{ csrf_field() }}
            <div class="col-sm-6">

                <input type="text" id="name_contact_us" class="f-input form-group form-control" name="name" placeholder="نام" />

                <input type="email"  id ="email_contact_us" class="f-input form-group form-control" name="email" placeholder="ایمیل" />

                <input type="number" id="number_contact_us" class="f-input form-group form-control"  name="mobile_number" placeholder="شماره تماس" />
            </div>
            <div class="col-sm-6">


                <textarea rows="7"  id="content_contact_us" class="form-control form-group f-textarea"  name="content" placeholder="متن پیام"></textarea>
            </div>
            <div class="submit text-center">
                <a class="btn btn-success btn-group btn-lg btn-block"  id="register_contact_us" >ارسال</a>
            </div>
        </form>

        <div id="error_contact_us"> </div>


    </div>


</div>

<script>
    $(document).ready(function() {



        $( "#register_contact_us" ).click(function () {



            var name_contact_us = $( "#name_contact_us" ).val();
            var email_contact_us = $( "#email_contact_us" ).val();
            var number_contact_us = $( "#number_contact_us" ).val();
            var content_contact_us = $( "#content_contact_us" ).val();

            var data ={};
//
            if (name_contact_us)
                data.name=(name_contact_us);
            if (email_contact_us)
                data.email=(email_contact_us);
            if (number_contact_us)
                data.mobile_number=(number_contact_us);
            if (content_contact_us)
                data.content=(content_contact_us);
//
            var myJSON = JSON.stringify(data);
            console.log(myJSON);
            $.ajax({
                type: "POST",
                url: 'send_mail',
                data :myJSON,
                success:function(data) {

                    $.each(data, function(key, value) {
                        console.log(value);

                        $( "#error_contact_us" ).empty();


                        if (value.content)
                            $( "#error_contact_us" ).text(value.content[0]);
                        if (value.email)
                            $( "#error_contact_us" ).text(value.email[0]);
                        if (value.mobile_number)
                            $( "#error_contact_us" ).text(value.mobile_number[0]);
                        if (value.name)
                            $( "#error_contact_us" ).text(value.name[0]);
                        if (key=='success')
                            $( "#error_contact_us" ).text(value);

                    });






                },
                dataType: "json"
            });


        })

    });
</script>
<footer>
    <div class="copyright text-center">
        تمامی حقوق این وبسایت متعلق به شرکت کمک دیهیم سبز می باشد ©     </div>
</footer>