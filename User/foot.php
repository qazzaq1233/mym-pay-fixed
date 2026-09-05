<?php if(isset($mym_iframe_mode) && $mym_iframe_mode){ ?>
        </div>
<?php }else{ ?>
</div> 
                                        </div>
                                    </div>                                                                   
                                </div> 
                            </div>
                            <!-- end row -->
                            
                        </div><!-- container -->

                    </div> <!-- Page content Wrapper -->

                </div> <!-- content -->

                <footer class="footer">
                    © 2021-2024 PAY  By <a href="https://g.9o3.cn/">MYM</a>.
                </footer>

            </div>
            <!-- End Right content here -->

        </div>
        <!-- END wrapper -->
<?php } ?>


        <!-- jQuery  -->
        <script src="./Assets/assets/js/jquery.min.js"></script>
        <script src="./Assets/assets/js/popper.min.js"></script>
        <script src="./Assets/assets/js/bootstrap.min.js"></script>
        <script src="./Assets/assets/js/modernizr.min.js"></script>
        <script src="./Assets/assets/js/detect.js"></script>
        <script src="./Assets/assets/js/fastclick.js"></script>
        <script src="./Assets/assets/js/jquery.slimscroll.js"></script>
        <script src="./Assets/assets/js/jquery.blockUI.js"></script>
        <script src="./Assets/assets/js/waves.js"></script>
        <script src="./Assets/assets/js/jquery.nicescroll.js"></script>
        <script src="./Assets/assets/js/jquery.scrollTo.min.js"></script>
        <script src="./Assets/assets/js/app.js"></script>
<script type="text/javascript">
  function pay_pass(){//POST提交
        var pay_pass= $("#pay_pass").val();
        var ii = layer.load(3, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "Ajax2.php?act=Pay_pass",
            data : {pay_pass},
            dataType : 'json',
            timeout:10000,
            success : function(data) {
                layer.close(ii);
                layer.msg(data.msg);
                if(data.code==1){
                    setTimeout(function () {
                        location.reload();
                    }, 1000); //延时1秒跳转
                }else if(data.code==0){
                    setTimeout(function () {
                        window.location.href = 'userinfo.php';
                    }, 1000); //延时1秒跳转
                }
            },
            error:function(data){
                layer.close(ii);
                layer.msg('服务器错误');
            }
        });
    }
</script>
        

    </body>
</html>