<div id="footer">

    <div class="inner">

        <p><?php _e('Copyright &copy;', APP_TD); ?> <?php echo date_i18n('Y'); ?> <?php bloginfo('name'); ?></p>

    </div><!-- end inner -->

</div><!-- end footer -->

<?php
wp_enqueue_script("jquery");
wp_enqueue_script('general', get_template_directory_uri() . '/includes/js/theme-scripts.js');
wp_enqueue_script('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js', array('jquery'), '1.8.6');
?>
<script type="text/javascript"
        src="http://maps.google.com/maps/api/js?v=3.2&sensor=false">
</script>

<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(function () {

        $j(".add-spreadsheet-column").click(function () {

            $j('.spreadsheet').find('tr').each(function () {
                $j(this).find('td').eq(0).after('<td class="text" contenteditable="true">Test Content</td>');
            });
        }); //click function

        $j(".add-spreadsheet-row").click(function () {

            var count = $j("tr:last td").length;
            var tdString = [];
            var column;
            for (i = 0; i < count; i++) {
                column += "<td class='text' contenteditable='true'>Test Content</td>";
            }


            $j('.spreadsheet > tbody:first').append('<tr>' + column + '</tr>');
        });
        $j('.alert-table-data').click(function () {

            var count_row = $j("tr").length;
            var count_column = $j("tr:last td").length;
            var spreadsheet_data = [];
            var spreadsheet_columns = [];
            var spreadsheet_cell;
            for (tableRow = 0; tableRow < count_row; tableRow++) {
                for (tableColumn = 0; tableColumn < count_column; tableColumn++) {

                    spreadsheet_cell = $j('.spreadsheet tr:eq(' + tableRow + ')').find('td:eq(' + tableColumn + ')').text();
                    spreadsheet_columns.push(spreadsheet_cell);
                }
                spreadsheet_data.push(spreadsheet_columns);
            }

            $j("#tableData").val("");
            $j("#tableData").val(JSON.stringify(spreadsheet_data));
        });
        $j(".submit").live("click", function () {

            var count_row = $j("tr").length;
            var count_column = $j("tr:last td").length;
            var spreadsheet_data = [];
            var spreadsheet_columns = [];
            var spreadsheet_cell;
            for (tableRow = 0; tableRow < count_row; tableRow++) {
                for (tableColumn = 0; tableColumn < count_column; tableColumn++) {

                    spreadsheet_cell = $j('.spreadsheet tr:eq(' + tableRow + ')').find('td:eq(' + tableColumn + ')').text();
                    spreadsheet_columns.push(spreadsheet_cell);
                }
                spreadsheet_data.push(spreadsheet_columns);
            }

            $j("#tableData").val(JSON.stringify(spreadsheet_data));
        });
        //for video evaluation form
        $j("#save_video_score").click(function () {

            var formData = $j(".video-evaluation-form").serialize();
            alert(formData);
            var ajaxurl = "/wp-admin/admin-ajax.php";
            $j.ajax({
                url: "/wp-admin/admin-ajax.php",
                type: "POST",
                data: formData + "&action=save_video_evaluation",
                beforeSend: function () {

                },
                success: function () {
                    window.location.reload();
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); //ajax

        });
        //for save evaluation form    
        $j("#save_score").click(function () {

            var formData = $j(".final-evaluation-form").serialize();
            var ajaxurl = "/wp-admin/admin-ajax.php";
            $j.ajax({
                url: "/wp-admin/admin-ajax.php",
                type: "POST",
                data: formData + "&action=save_evaluation",
                beforeSend: function () {

                },
                success: function () {
                    window.location.reload();
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); //ajax

        });
        /* 
         * Function to get highest rated resumes and sort them by job
         */

        var wrapper = $j("#employer_evaluation .resumes");
        var listItems = wrapper.children('li').get();
        listItems.sort(function (a, b) {
            var evaluation_rating = parseInt($j(a).find('.final-evaluation-with-rating').text());
            var prev_evaluation_rating = parseInt($j(b).find('.final-evaluation-with-rating').text());
            return (evaluation_rating < prev_evaluation_rating) ? -1 : (evaluation_rating > prev_evaluation_rating) ? 1 : 0;
        });
        listItems.reverse();
        $j(listItems).appendTo(wrapper);
        $j(".jobs_dropdown").change(function () {


            var resume_count = $j("#employer_evaluation .resumes li").length;
            for (i = 0; i < resume_count; i++) {

                var job_title = $j("#employer_evaluation .resumes li:eq(" + i + ") .job_applying_for_link").text();
                var dropdown_title = $j(".jobs_dropdown option:selected").text();
                if (dropdown_title === "All Resumes") {
                    $j("#employer_evaluation .resumes li:eq(" + i + ")").show();
                } else if (dropdown_title === "All Recent Resumes") {

                    var wrapper = $j("#employer_evaluation .resumes");
                    var listItems = wrapper.children('li').get();
                    listItems.sort(function (a, b) {
                        var a_date = $j(a).find('.resume_date').text();
                        var b_date = $j(b).find('.resume_date').text();
                        return new Date(a_date) > new Date(b_date);
                    });
                    listItems.reverse();
                    $j(listItems).appendTo(wrapper);
                } else {
                    if (job_title !== dropdown_title) {
                        $j("#employer_evaluation .resumes li:eq(" + i + ")").hide();
                    } else {
                        $j("#employer_evaluation .resumes li:eq(" + i + ")").show();
                    }

                }

            } /*for resume count */
        });
        $j("#resumes").on("click", ".delete-resume", function () {

            resume_id = $j(this).attr("href");
            index = $j(this).parent().parent().index();
            var answer = confirm("<?php _e('Are you sure you want to delete this resume? This action cannot be undone.', APP_TD); ?>");
            if (answer) {

                $j.ajax({
                    url: "/wp-admin/admin-ajax.php",
                    type: "POST",
                    data: "resume_id=" + resume_id + "&action=resume_delete",
                    beforeSend: function () {

                    },
                    success: function () {
                        //window.location.reload();

                        $j(".data_list tbody tr:eq(" + index + ")").remove();
                    },
                    error: function (xhr, status, error) {
                        alert(xhr.responseText);
                    }
                }); //ajax

            } else {
                return false;
            }

            return false;
        });
        //$j("#live .jobs .job .actions .end").click(function () {
        $j("#live").on("click", ".end", function () {
            var job_id_index = $j(this).parent().parent().parent().index();
            var job_id = $j("#live .jobs .job:eq(" + job_id_index + ") .actions_job_id").val();
            $j.ajax({
                url: "/wp-admin/admin-ajax.php",
                type: "POST",
                data: "job_id=" + job_id + "&action=job_end",
                beforeSend: function () {

                },
                success: function () {
                    //window.location.reload();


                    var live_current_page_number;
                    var ended_current_page_number;
                    /*Reload Live div*/
                    if ($j("#live .wp-pagenavi").length) {
                        live_current_page_number = $j("#live .wp-pagenavi span").text();
                    } else {
                        live_current_page_number = 1;
                    }

                    /*Reload Ended Div*/
                    if ($j("#ended .wp-pagenavi").length) {
                        ended_current_page_number = $j("#ended .wp-pagenavi span").text();
                    } else {
                        ended_current_page_number = 1;
                    }

                    var live_link = "/page/" + live_current_page_number + "/?tab=live";
                    var ended_link = "/page/" + ended_current_page_number + "/?tab=ended";
                    $j("#live .jobs").load(live_link + " .job-live", {"paged-live": live_current_page_number}, function () {
                        //$j(this).fadeIn("2000");
                    });
                    $j("#ended .jobs").load(ended_link + " .job-ended", {"paged-ended": ended_current_page_number}, function () {
                        //$j(this).fadeIn("2000");
                    });
                    //$j("#live .jobs .job:eq(" + job_id_index + ")").appendTo("#ended .jobs");
                    $j("#live .jobs .job:eq(" + job_id_index + ")").remove();
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); //ajax*/

            return false;
        });
        //$j("#ended .jobs .job .actions .delete").click(function () {
        $j("#ended").on("click", ".delete", function () {
            var job_id_index = $j(this).parent().parent().parent().index();
            var job_id = $j("#ended .jobs .job:eq(" + job_id_index + ") .actions_job_id").val();
            $j.ajax({
                url: "/wp-admin/admin-ajax.php",
                type: "POST",
                data: "job_id=" + job_id + "&action=job_delete",
                beforeSend: function () {

                },
                success: function () {
                    //window.location.reload();


                    var live_current_page_number;
                    var ended_current_page_number;
                    /*Reload Live div*/
                    if ($j("#live .wp-pagenavi").length) {
                        live_current_page_number = $j("#live .wp-pagenavi span").text();
                    } else {
                        live_current_page_number = 1;
                    }

                    /*Reload Ended Div*/
                    if ($j("#ended .wp-pagenavi").length) {
                        ended_current_page_number = $j("#ended .wp-pagenavi span").text();
                    } else {
                        ended_current_page_number = 1;
                    }

                    var live_link = "/page/" + live_current_page_number + "/?tab=live";
                    var ended_link = "/page/" + ended_current_page_number + "/?tab=ended";
                    $j("#live .jobs").load(live_link + " .job-live", {"paged-live": live_current_page_number}, function () {
                        //$j(this).fadeIn("2000");
                    });
                    $j("#ended .jobs").load(ended_link + " .job-ended", {"paged-ended": ended_current_page_number}, function () {
                        //$j(this).fadeIn("2000");
                    });
                    $j("#ended .jobs .job:eq(" + job_id_index + ")").remove();
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); //ajax*/

            return false;
        });
        //$j("#ended .jobs .job .actions .relist").click(function () {
        $j("#ended").on("click", ".relist", function () {
            var job_id_index = $j(this).parent().parent().parent().index();
            var job_id = $j("#ended .jobs .job:eq(" + job_id_index + ") .actions_job_id").val();
            $j.ajax({
                url: "/wp-admin/admin-ajax.php",
                type: "POST",
                data: "job_id=" + job_id + "&action=job_relisting",
                beforeSend: function () {

                },
                success: function () {
                    //window.location.reload();

                    var live_current_page_number;
                    var ended_current_page_number;
                    /*Reload Live div*/
                    if ($j("#live .wp-pagenavi").length) {
                        live_current_page_number = $j("#live .wp-pagenavi span").text();
                    } else {
                        live_current_page_number = 1;
                    }

                    /*Reload Ended Div*/
                    if ($j("#ended .wp-pagenavi").length) {
                        ended_current_page_number = $j("#ended .wp-pagenavi span").text();
                    } else {
                        ended_current_page_number = 1;
                    }

                    var live_link = "/page/" + live_current_page_number + "/?tab=live";
                    var ended_link = "/page/" + ended_current_page_number + "/?tab=ended";
                    $j("#live .jobs").load(live_link + " .job-live", {"paged-live": live_current_page_number}, function () {
                        //$j(this).fadeIn("2000");
                    });
                    $j("#ended .jobs").load(ended_link + " .job-ended", {"paged-ended": ended_current_page_number}, function () {
                        //$j(this).fadeIn("2000");
                    });
                    //$j("#ended .jobs .job:eq(" + job_id_index + ")").appendTo("#live .jobs");
                    $j("#ended .jobs .job:eq(" + job_id_index + ")").remove();
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); //ajax*/

            return false;
        });
        $j("#ended").on("click", ".wp-pagenavi a", function (e) {
            e.preventDefault();
            /*
             * Replace current span with a link to previous current page
             * 
             */
            var link = $j(this).attr("href");
            var prev_page_number = $j("#ended .wp-pagenavi span.current").text();
            var domain = "/page/" + prev_page_number + "/?tab=ended";
            var current_page_number = $j(this).text();
            $j("#ended .jobs").load(link + " .job-ended", {"paged-ended": current_page_number}, function () {
                //$j(this).fadeIn(500);
            });
            $j("#ended .wp-pagenavi span.current").replaceWith("<a href='" + domain + "'>" + prev_page_number + "</a>");
            $j(this).replaceWith("<span class='current'>" + current_page_number + "</span>");
        });
        $j("#live").on("click", ".wp-pagenavi a", function (e) {
            e.preventDefault();
            /*
             * Replace current span with a link to previous current page
             * 
             */
            var link = $j(this).attr("href");
            var prev_page_number = $j("#live .wp-pagenavi span.current").text();
            var domain = "/page/" + prev_page_number + "/?tab=live";
            var current_page_number = $j(this).text();
            $j("#live .jobs").load(link + " .job-live", {"paged-live": current_page_number}, function () {
                //$j(this).fadeIn(500);
            });
            $j("#live .wp-pagenavi span.current").replaceWith("<a href='" + domain + "'>" + prev_page_number + "</a>");
            $j(this).replaceWith("<span class='current'>" + current_page_number + "</span>");
        });
        /*
         * Function for Toggling Statuses
         * 
         */
        $j(".toggle-processing-status").on("click", "a", function (e) {
            e.preventDefault();
            var $t = $j(this);
            var status_class = $j(this).attr("class");
            var status_text = $j(this).text();
            var resume_id = $j(this).parent().parent().parent().parent().parent().parent().parent().find(".resume_id").val();
            var employer_id = $j(this).parent().parent().parent().parent().parent().parent().parent().find(".employer_id").val();
            $j.ajax({
                url: "/wp-admin/admin-ajax.php",
                type: "POST",
                data: "resume_id=" + resume_id + "&employer_id=" + employer_id + "&resume_status=" + status_class + "&status_text=" + status_text + "&action=change_resume_statuses",
                beforeSend: function () {

                },
                success: function (data) {
                    domain = window.location.href;
                    data_length = data.length - 1;
                    response = data.substr(0, data_length);
                    $t.parent().html(response);
                    $j("#employer_evaluation").load(domain + " #employer_evaluation #jobs_dropdown_div,#employer_evaluation .resumes", function () {
                        toggle_processing_statuses();
                        job_drop_down_func();
                    });
                    $j("#employer_resumes").load(domain + " #employer_resumes h3,#employer_resumes .resumes", function () {
                        toggle_processing_statuses();
                    });
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); //ajax*/



        });
        function toggle_processing_statuses() {

            $j(".toggle-processing-status").on("click", "a", function (e) {
                e.preventDefault();
                var $t = $j(this);
                var status_class = $j(this).attr("class");
                var status_text = $j(this).text();
                var resume_id = $j(this).parent().parent().parent().parent().parent().parent().parent().find(".resume_id").val();
                var employer_id = $j(this).parent().parent().parent().parent().parent().parent().parent().find(".employer_id").val();
                $j.ajax({
                    url: "/wp-admin/admin-ajax.php",
                    type: "POST",
                    data: "resume_id=" + resume_id + "&employer_id=" + employer_id + "&resume_status=" + status_class + "&status_text=" + status_text + "&action=change_resume_statuses",
                    beforeSend: function () {

                    },
                    success: function (data) {
                        domain = window.location.href;
                        data_length = data.length - 1;
                        response = data.substr(0, data_length);
                        $t.parent().html(response);
                        $j("#employer_evaluation").load(domain + " #employer_evaluation #jobs_dropdown_div,#employer_evaluation .resumes", function () {
                            toggle_processing_statuses();
                            job_drop_down_func();
                        });
                        $j("#employer_resumes").load(domain + " #employer_resumes h3,#employer_resumes .resumes", function () {
                            toggle_processing_statuses();
                        });
                    },
                    error: function (xhr, status, error) {
                        alert(xhr.responseText);
                    }
                }); //ajax*/



            });
        }

        function job_drop_down_func() {

            /* 
             * Function to get highest rated resumes and sort them by job
             */

            var wrapper = $j("#employer_evaluation .resumes");
            var listItems = wrapper.children('li').get();
            listItems.sort(function (a, b) {
                var evaluation_rating = parseInt($j(a).find('.final-evaluation-with-rating').text());
                var prev_evaluation_rating = parseInt($j(b).find('.final-evaluation-with-rating').text());
                return (evaluation_rating < prev_evaluation_rating) ? -1 : (evaluation_rating > prev_evaluation_rating) ? 1 : 0;
            });
            listItems.reverse();
            $j(listItems).appendTo(wrapper);
            $j(".jobs_dropdown").change(function () {

                var resume_count = $j("#employer_evaluation .resumes li").length;
                for (i = 0; i < resume_count; i++) {

                    var job_title = $j("#employer_evaluation .resumes li:eq(" + i + ") .job_applying_for_link").text();
                    var dropdown_title = $j(".jobs_dropdown option:selected").text();
                    if (dropdown_title === "All Recent Evaluations") {
                        $j("#employer_evaluation .resumes li:eq(" + i + ")").show();
                    } else if (dropdown_title === "All Recent Resumes") {

                        var wrapper = $j("#employer_evaluation .resumes");
                        var listItems = wrapper.children('li').get();
                        listItems.sort(function (a, b) {
                            var a_date = $j(a).find('.resume_date').text();
                            var b_date = $j(b).find('.resume_date').text();
                            return new Date(a_date) > new Date(b_date);
                        });
                        listItems.reverse();
                        $j(listItems).appendTo(wrapper);
                    } else {
                        if (job_title !== dropdown_title) {
                            $j("#employer_evaluation .resumes li:eq(" + i + ")").hide();
                        } else {
                            $j("#employer_evaluation .resumes li:eq(" + i + ")").show();
                        }

                    }

                } //for resume count
            });
        }

        $j(".sort_by_date").change(function () {

            if ($j(".sort_by_date:checked").length > 0) {

                var wrapper = $j("#employer_evaluation .resumes");
                var listItems = wrapper.children('li').get();
                listItems.sort(function (a, b) {
                    var a_date = $j(a).find('.resume_date').text();
                    var b_date = $j(b).find('.resume_date').text();
                    var a_year = a_date.substr(8, 12).toString();
                    var a_month = a_date.substr(2, 4).toString();
                    var a_day = a_date.substr(0, 2).toString();
                    var b_year = b_date.substr(8, 12).toString();
                    var b_month = b_date.substr(2, 4).toString();
                    var b_day = b_date.substr(0, 2).toString();
                    var a_date_format = new Date(a_month + a_day + "," + a_year);
                    var b_date_format = new Date(b_month + b_day + "," + b_year);
                    return  a_date_format < b_date_format;
                });
                $j(listItems).appendTo(wrapper);
            } else {


                var wrapper = $j("#employer_evaluation .resumes");
                var listItems = wrapper.children('li').get();
                listItems.sort(function (a, b) {
                    var evaluation_rating = parseInt($j(a).find('.final-evaluation-with-rating').text());
                    var prev_evaluation_rating = parseInt($j(b).find('.final-evaluation-with-rating').text());
                    return (evaluation_rating < prev_evaluation_rating) ? -1 : (evaluation_rating > prev_evaluation_rating) ? 1 : 0;
                });
                listItems.reverse();
                $j(listItems).appendTo(wrapper);
            }

        });
        $j("#employer_applicant_discussions").on("click", ".reply", function (e) {
            e.preventDefault();
            comment_index = $j(this).parent().parent().index();
            $j(this).parent().parent().parent().find(".reply-box").toggle();
        });
        $j("#employer_applicant_discussions").on("click", ".submit-reply", function (e) {
            e.preventDefault();
            comment_index = $j(this).parent().parent().index();
            //$j(this).parent().parent().find(".reply-box").toggle();

            post_id = $j(this).parent().parent().parent().find(".post_id").val();
            name = $j(this).parent().parent().parent().find(".name").val();
            email = $j(this).parent().parent().parent().find(".email").val();
            user_id = $j(this).parent().parent().parent().find(".user_id").val();
            content = $j(this).parent().parent().parent().find(".reply-content").val();
            if (content != "") {


                $j.ajax({
                    url: "/wp-admin/admin-ajax.php",
                    type: "POST",
                    data: "name=" + name + "&email=" + email + "&user_id=" + user_id + "&content=" + content + "&post_id=" + post_id + "&action=post_comment_ajax",
                    beforeSend: function () {

                    },
                    success: function (data) {
                        domain = window.location.href;
                        data_length = data.length - 1;
                        response = data.substr(0, data_length);
                        $j("#employer_applicant_discussions").load(domain + " #employer_applicant_discussions");
                    },
                    error: function (xhr, status, error) {
                        alert(xhr.responseText);
                    }
                }); //ajax*/

            }
        });
        $j("#employer_job_discussions").on("click", ".reply", function (e) {
            e.preventDefault();
            comment_index = $j(this).parent().parent().index();
            $j(this).parent().parent().parent().find(".reply-box").toggle();
        });
        $j("#employer_job_discussions").on("click", ".submit-reply", function (e) {
            e.preventDefault();
            comment_index = $j(this).parent().parent().index();
            //$j(this).parent().parent().find(".reply-box").toggle();

            post_id = $j(this).parent().parent().parent().find(".post_id").val();
            name = $j(this).parent().parent().parent().find(".name").val();
            email = $j(this).parent().parent().parent().find(".email").val();
            user_id = $j(this).parent().parent().parent().find(".user_id").val();
            content = $j(this).parent().parent().parent().find(".reply-content").val();
            $j.ajax({
                url: "/wp-admin/admin-ajax.php",
                type: "POST",
                data: "name=" + name + "&email=" + email + "&user_id=" + user_id + "&content=" + content + "&post_id=" + post_id + "&action=post_comment_ajax",
                beforeSend: function () {

                },
                success: function (data) {
                    domain = window.location.href;
                    data_length = data.length - 1;
                    response = data.substr(0, data_length);
                    $j("#employer_job_discussions").load(domain + " #employer_job_discussions");
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); //ajax*/
        });
        $j(".email_reference").on("click", function (e) {
            e.preventDefault();
            var index = $j(this).parent().index() - 1;
            var email = $j(this).parent().parent().siblings().find(".reference_email").eq(index).text();
            var name = $j(this).parent().parent().siblings().find(".reference_name").eq(index).text();
            var employee_name = $j(".email-from").val();
            $j(".reference-dialog").attr("style", "display:inline");
            $j(".email-address").val(email);
            $j(".reference-name").val(name);
            $j("text.reference-name").text(name);
            //$j(".mail-content").text(mail_content);
        });
        $j(".reference-send-button").on("click", function () {

            var to = $j(".email-address").val();
            var subject = $j(".email-subject").val();
            var message = $j(".mail-content").html();
            var resume_id = $j(".reference-resume-id").val();
            var reference_name = $j(".reference-name").val();
            $j.ajax({
                url: "/wp-admin/admin-ajax.php",
                type: "POST",
                data: "to=" + to + "&subject=" + subject + "&message=" + message + "&resume_id=" + resume_id + "&reference_name=" + reference_name + "&action=send_email_ajax",
                beforeSend: function () {

                },
                success: function (data) {
                    domain = window.location.href;
                    data_length = data.length - 1;
                    response = data.substr(0, data_length);
                    alert("email sent");
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); //ajax*/
        });
        $j(".reference-close-button").on("click", function () {
            $j(".reference-dialog").attr("style", "display:none");
        });
        $j(".button_edit_resume").on("click", function () {
            var edit_link = $j(".edit_link").val();
            window.location.href = edit_link;
        });

        // For Apply Job

        $j(".apply_for_job").click(function (e) {
            e.preventDefault();
            $j(".apply_for_job_div").slideToggle();
        });

        $j(".apply_for_job_submit").click(function (e) {
            e.preventDefault();
            
            resume_id = $j(".apply_for_job_dropdown option:selected").val();
            
            resume = $j(".apply_for_job_dropdown option:selected").text();
            
            job = $j(".apply_for_job_form .job_title").val();
            
            $j.ajax({
                url: "/wp-admin/admin-ajax.php",
                type: "POST",
                data: "resume_id="+resume_id+"&resume_name="+resume+"&job="+job+"&action=apply_for_job",
                beforeSend: function () {

                },
                success: function (data) {
                    alert("application sent");
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            }); //ajax*/
        });

        $j(".toggleMap").click({
           //$j("#job_map").attr("style","position: absolute;left: 100%;"") 
        });
        
        $j(".job_save").click(function() {
            $j("#submit_form").submit();
        });

    }); //no conflict function 		

    try {
        document.getElementById('login_username').focus();
    } catch (e) {
    }

    Date.prototype.format = function (format) {
        var returnStr = '';
        var replace = Date.replaceChars;
        for (var i = 0; i < format.length; i++) {
            var curChar = format.charAt(i);
            if (i - 1 >= 0 && format.charAt(i - 1) == "\\") {
                returnStr += curChar;
            }
            else if (replace[curChar]) {
                returnStr += replace[curChar].call(this);
            } else if (curChar != "\\") {
                returnStr += curChar;
            }
        }
        return returnStr;
    };
    Date.replaceChars = {
        shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        longMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        shortDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        longDays: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        // Day
        d: function () {
            return (this.getDate() < 10 ? '0' : '') + this.getDate();
        },
        D: function () {
            return Date.replaceChars.shortDays[this.getDay()];
        },
        j: function () {
            return this.getDate();
        },
        l: function () {
            return Date.replaceChars.longDays[this.getDay()];
        },
        N: function () {
            return this.getDay() + 1;
        },
        S: function () {
            return (this.getDate() % 10 == 1 && this.getDate() != 11 ? 'st' : (this.getDate() % 10 == 2 && this.getDate() != 12 ? 'nd' : (this.getDate() % 10 == 3 && this.getDate() != 13 ? 'rd' : 'th')));
        },
        w: function () {
            return this.getDay();
        },
        z: function () {
            var d = new Date(this.getFullYear(), 0, 1);
            return Math.ceil((this - d) / 86400000);
        }, // Fixed now
        // Week
        W: function () {
            var d = new Date(this.getFullYear(), 0, 1);
            return Math.ceil((((this - d) / 86400000) + d.getDay() + 1) / 7);
        }, // Fixed now
        // Month
        F: function () {
            return Date.replaceChars.longMonths[this.getMonth()];
        },
        m: function () {
            return (this.getMonth() < 9 ? '0' : '') + (this.getMonth() + 1);
        },
        M: function () {
            return Date.replaceChars.shortMonths[this.getMonth()];
        },
        n: function () {
            return this.getMonth() + 1;
        },
        t: function () {
            var d = new Date();
            return new Date(d.getFullYear(), d.getMonth(), 0).getDate()
        }, // Fixed now, gets #days of date
        // Year
        L: function () {
            var year = this.getFullYear();
            return (year % 400 == 0 || (year % 100 != 0 && year % 4 == 0));
        }, // Fixed now
        o: function () {
            var d = new Date(this.valueOf());
            d.setDate(d.getDate() - ((this.getDay() + 6) % 7) + 3);
            return d.getFullYear();
        }, //Fixed now
        Y: function () {
            return this.getFullYear();
        },
        y: function () {
            return ('' + this.getFullYear()).substr(2);
        },
        // Time
        a: function () {
            return this.getHours() < 12 ? 'am' : 'pm';
        },
        A: function () {
            return this.getHours() < 12 ? 'AM' : 'PM';
        },
        B: function () {
            return Math.floor((((this.getUTCHours() + 1) % 24) + this.getUTCMinutes() / 60 + this.getUTCSeconds() / 3600) * 1000 / 24);
        }, // Fixed now
        g: function () {
            return this.getHours() % 12 || 12;
        },
        G: function () {
            return this.getHours();
        },
        h: function () {
            return ((this.getHours() % 12 || 12) < 10 ? '0' : '') + (this.getHours() % 12 || 12);
        },
        H: function () {
            return (this.getHours() < 10 ? '0' : '') + this.getHours();
        },
        i: function () {
            return (this.getMinutes() < 10 ? '0' : '') + this.getMinutes();
        },
        s: function () {
            return (this.getSeconds() < 10 ? '0' : '') + this.getSeconds();
        },
        u: function () {
            var m = this.getMilliseconds();
            return (m < 10 ? '00' : (m < 100 ?
                    '0' : '')) + m;
        },
        // Timezone
        e: function () {
            return "Not Yet Supported";
        },
        I: function () {
            var DST = null;
            for (var i = 0; i < 12; ++i) {
                var d = new Date(this.getFullYear(), i, 1);
                var offset = d.getTimezoneOffset();
                if (DST === null)
                    DST = offset;
                else if (offset < DST) {
                    DST = offset;
                    break;
                } else if (offset > DST)
                    break;
            }
            return (this.getTimezoneOffset() == DST) | 0;
        },
        O: function () {
            return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + '00';
        },
        P: function () {
            return (-this.getTimezoneOffset() < 0 ? '-' : '+') + (Math.abs(this.getTimezoneOffset() / 60) < 10 ? '0' : '') + (Math.abs(this.getTimezoneOffset() / 60)) + ':00';
        }, // Fixed now
        T: function () {
            var m = this.getMonth();
            this.setMonth(0);
            var result = this.toTimeString().replace(/^.+ \(?([^\)]+)\)?$/, '$1');
            this.setMonth(m);
            return result;
        },
        Z: function () {
            return -this.getTimezoneOffset() * 60;
        },
        // Full Date/Time
        c: function () {
            return this.format("Y-m-d\\TH:i:sP");
        }, // Fixed now
        r: function () {
            return this.toString();
        },
        U: function () {
            return this.getTime() / 1000;
        }
    };
</script>


