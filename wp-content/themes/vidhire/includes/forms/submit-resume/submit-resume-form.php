<?php

/**
 * JobRoller Resume form
 * Function outputs the resume submit form
 *
 *
 * @version 1.4
 * @author AppThemes
 * @package JobRoller
 * @copyright 2010 all rights reserved
 *
 */
//add_action('wp_loaded', 'jr_submit_resume_form');
function jr_submit_resume_form($resume_id = 0) {

    global $post, $posted;

    jr_geolocation_scripts();
    ?>
    <form action="<?php
    if ($resume_id > 0) {

        echo add_query_arg('edit', $resume_id, get_permalink($post->ID));
    } else {
        echo get_permalink($post->ID);
        //echo add_query_arg('edit', $resume_id, get_permalink($post->ID));
    }
    ?>" method="post" enctype="multipart/form-data" id="submit_form" class="submit_form main_form">

        <p><?php _e('Once saved you will be able to view your resume and edit at a later date.', APP_TD); ?></p>

        <fieldset>
            <legend><?php _e('Your Resume', APP_TD); ?></legend>

            <p><label for="resume_name"><?php _e('Full Name', APP_TD); ?> <span title="required">*</span></label> <input type="text" class="text" name="resume_name" id="resume_name" class="text" placeholder="<?php _e('e.g. John H. Doe', APP_TD); ?>" value="<?php if (isset($posted['resume_name'])) echo $posted['resume_name']; ?>" /></p>

            <p style="display:none"><label for="summary"><?php _e('Objective', APP_TD); ?> <span title="optional">*</span></label> <textarea rows="5" cols="30" name="summary" id="summary" placeholder="<?php _e('Describe your career objectives.', APP_TD); ?>" class="short" style="height:100px;"><?php if (isset($posted['summary'])) echo $posted['summary']; ?></textarea></p>

            <p class="optional"><label for="resume_cat"><?php _e('Job You Are Applying For', APP_TD); ?></label> <?php
                $sel = 0;
                if (isset($posted['resume_cat']) && $posted['resume_cat'] > 0) {
                    $sel = $posted['resume_cat'];
                }

                $args = array(
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'name' => 'resume_cat',
                    'hierarchical' => 1,
                    'echo' => 0,
                    'class' => 'resume_cat',
                    'selected' => $sel,
                    'taxonomy' => 'resume_category',
                    'hide_empty' => false
                );
                $dropdown = wp_dropdown_categories($args);
                $dropdown = str_replace('class=\'resume_cat\' >', 'class=\'resume_cat\' ><option value="">' . __('Select a Job&hellip;', APP_TD) . '</option>', $dropdown);
                echo $dropdown;
                ?></p>

            <p class="optional"><label for="your-photo"><?php _e('Resume Photo (.jpg, .gif or .png)', APP_TD); ?></label> <input type="file" class="text" name="your-photo" id="your-photo" /></p>
            <p class="optional"><label for="your-resume"><?php _e('Resume(.doc or .docx)', APP_TD); ?></label> <input type="file" class="text" name="your-resume" id="your-resume" /></p>
            <p class="optional"><label for="misc-documents"><?php _e('Miscellaneous Documents(.jpg, gif or .png)', APP_TD); ?></label> <input type="file" class="text" name="misc-documents" id="misc-documents" /></p>

            <?php
            $currencies = array(
                'ALL' => 'Albania Lek',
                'AFN' => 'Afghanistan Afghani',
                'ARS' => 'Argentina Peso',
                'AWG' => 'Aruba Guilder',
                'AUD' => 'Australia Dollar',
                'AZN' => 'Azerbaijan New Manat',
                'BSD' => 'Bahamas Dollar',
                'BBD' => 'Barbados Dollar',
                'BDT' => 'Bangladeshi taka',
                'BYR' => 'Belarus Ruble',
                'BZD' => 'Belize Dollar',
                'BMD' => 'Bermuda Dollar',
                'BOB' => 'Bolivia Boliviano',
                'BAM' => 'Bosnia and Herzegovina Convertible Marka',
                'BWP' => 'Botswana Pula',
                'BGN' => 'Bulgaria Lev',
                'BRL' => 'Brazil Real',
                'BND' => 'Brunei Darussalam Dollar',
                'KHR' => 'Cambodia Riel',
                'CAD' => 'Canadian Dollar',
                'KYD' => 'Cayman Islands Dollar',
                'CLP' => 'Chile Peso',
                'CNY' => 'China Yuan Renminbi',
                'COP' => 'Colombia Peso',
                'CRC' => 'Costa Rica Colon',
                'HRK' => 'Croatia Kuna',
                'CUP' => 'Cuba Peso',
                'CZK' => 'Czech Republic Koruna',
                'DKK' => 'Denmark Krone',
                'DOP' => 'Dominican Republic Peso',
                'XCD' => 'East Caribbean Dollar',
                'EGP' => 'Egypt Pound',
                'SVC' => 'El Salvador Colon',
                'EEK' => 'Estonia Kroon',
                'EUR' => 'Euro Member Countries',
                'FKP' => 'Falkland Islands (Malvinas) Pound',
                'FJD' => 'Fiji Dollar',
                'GHC' => 'Ghana Cedis',
                'GIP' => 'Gibraltar Pound',
                'GTQ' => 'Guatemala Quetzal',
                'GGP' => 'Guernsey Pound',
                'GYD' => 'Guyana Dollar',
                'HNL' => 'Honduras Lempira',
                'HKD' => 'Hong Kong Dollar',
                'HUF' => 'Hungary Forint',
                'ISK' => 'Iceland Krona',
                'INR' => 'India Rupee',
                'IDR' => 'Indonesia Rupiah',
                'IRR' => 'Iran Rial',
                'IMP' => 'Isle of Man Pound',
                'ILS' => 'Israel Shekel',
                'JMD' => 'Jamaica Dollar',
                'JPY' => 'Japan Yen',
                'JEP' => 'Jersey Pound',
                'KZT' => 'Kazakhstan Tenge',
                'KPW' => 'Korea (North) Won',
                'KRW' => 'Korea (South) Won',
                'KGS' => 'Kyrgyzstan Som',
                'LAK' => 'Laos Kip',
                'LVL' => 'Latvia Lat',
                'LBP' => 'Lebanon Pound',
                'LRD' => 'Liberia Dollar',
                'LTL' => 'Lithuania Litas',
                'MKD' => 'Macedonia Denar',
                'MYR' => 'Malaysia Ringgit',
                'MUR' => 'Mauritius Rupee',
                'MXN' => 'Mexico Peso',
                'MNT' => 'Mongolia Tughrik',
                'MZN' => 'Mozambique Metical',
                'NAD' => 'Namibia Dollar',
                'NPR' => 'Nepal Rupee',
                'ANG' => 'Netherlands Antilles Guilder',
                'NZD' => 'New Zealand Dollar',
                'NIO' => 'Nicaragua Cordoba',
                'NGN' => 'Nigeria Naira',
                'NOK' => 'Norway Krone',
                'OMR' => 'Oman Rial',
                'PKR' => 'Pakistan Rupee',
                'PAB' => 'Panama Balboa',
                'PYG' => 'Paraguay Guarani',
                'PEN' => 'Peru Nuevo Sol',
                'PHP' => 'Philippines Peso',
                'PLN' => 'Poland Zloty',
                'QAR' => 'Qatar Riyal',
                'RON' => 'Romania New Leu',
                'RUB' => 'Russia Ruble',
                'SHP' => 'Saint Helena Pound',
                'SAR' => 'Saudi Arabia Riyal',
                'RSD' => 'Serbia Dinar',
                'SCR' => 'Seychelles Rupee',
                'SGD' => 'Singapore Dollar',
                'SBD' => 'Solomon Islands Dollar',
                'SOS' => 'Somalia Shilling',
                'ZAR' => 'South Africa Rand',
                'LKR' => 'Sri Lanka Rupee',
                'SEK' => 'Sweden Krona',
                'CHF' => 'Switzerland Franc',
                'SRD' => 'Suriname Dollar',
                'SYP' => 'Syria Pound',
                'TWD' => 'Taiwan New Dollar',
                'THB' => 'Thailand Baht',
                'TTD' => 'Trinidad and Tobago Dollar',
                'TRY' => 'Turkey Lira',
                'TRL' => 'Turkey Lira',
                'TVD' => 'Tuvalu Dollar',
                'UAH' => 'Ukraine Hryvna',
                'GBP' => 'United Kingdom Pound',
                'USD' => 'United States Dollar',
                'UYU' => 'Uruguay Peso',
                'UZS' => 'Uzbekistan Som',
                'VEF' => 'Venezuela Bolivar',
                'VND' => 'Viet Nam Dong',
                'YER' => 'Yemen Rial',
                'ZWD' => 'Zimbabwe Dollar'
            );
            ?>

            <p class="optional">
                <label for="currency"><?php _e('Currency', APP_TD); ?></label>
                <select class="currency" name="currency"> 
                    <?php
                    foreach ($currencies as $key => $value) {

                        if (isset($posted['currency']) && $posted['currency'] == $key) {
                            ?>
                            <option value="<?php echo $key; ?>" selected="selected"><?php echo $value . "(" . $key . ")"; ?></option>

                        <?php } else { ?>

                            <option value="<?php echo $key; ?>"><?php echo $value . "(" . $key . ")"; ?></option>

                        <?php } ?>
                    <?php } ?>
                </select>
            </p> 


            <p class="optional"><label for="desired_salary"><?php _e('Minimum Acceptable Hourly Rate (only numeric values)', APP_TD); ?></label> <input type="text" class="tags text" name="desired_salary" id="desired_salary" placeholder="<?php _e('e.g. 10', APP_TD); ?>" value="<?php if (isset($posted['desired_salary'])) echo $posted['desired_salary']; ?>" /></p>

            <p class="optional"><label for="desired_position"><?php _e('Desired Type of Position', APP_TD); ?></label> <select name="desired_position" id="desired_position">
                    <option value=""><?php _e('Any', APP_TD); ?></option>
                    <?php
                    $job_types = get_terms('resume_job_type', array('hide_empty' => '0'));
                    if ($job_types && sizeof($job_types) > 0) {
                        foreach ($job_types as $type) {
                            ?>
                            <option <?php if (isset($posted['desired_position']) && $posted['desired_position'] == $type->slug) echo 'selected="selected"'; ?> value="<?php echo $type->slug; ?>"><?php echo $type->name; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select></p>

        </fieldset>

        <fieldset>
            <legend><?php _e('Your Contact Details', APP_TD); ?></legend>

            <p><?php _e('Optionally fill in your contact details below to have them appear on your resume. This is important if you want employers to be able to contact you!', APP_TD); ?></p>

            <p class="optional"><label for="email_address"><?php _e('Email Address', APP_TD); ?></label> <input type="text" class="text" name="email_address" value="<?php if (isset($posted['email_address'])) echo $posted['email_address']; ?>" id="email_address" placeholder="<?php _e('you@yourdomain.com', APP_TD); ?>" /></p>
            <p class="optional"><label for="tel"><?php _e('Telephone', APP_TD); ?></label> <input type="text" class="text" name="tel" value="<?php if (isset($posted['tel'])) echo $posted['tel']; ?>" id="tel" placeholder="<?php _e('Telephone including area code', APP_TD); ?>" /></p>
            <p class="optional"><label for="mobile"><?php _e('Mobile', APP_TD); ?></label> <input type="text" class="text" name="mobile" value="<?php if (isset($posted['mobile'])) echo $posted['mobile']; ?>" id="mobile" placeholder="<?php _e('Mobile number', APP_TD); ?>" /></p>
            <p class="optional"><label for="mobile"><?php _e('Skype', APP_TD); ?></label> <input type="text" class="text" name="skype" value="<?php if (isset($posted['skype'])) echo $posted['skype']; ?>" id="skype" placeholder="<?php _e('Skype ID', APP_TD); ?>" /></p>


        </fieldset>

        <fieldset>
            <legend><?php _e('Resume Location', APP_TD); ?></legend>
            <p><?php _e('Entering your location will help employers find you.', APP_TD); ?></p>
            <div id="geolocation_box">
                <p>
                    <label>
                        <input id="geolocation-load" type="button" class="button geolocationadd submit" value="<?php esc_attr_e('Find Address/Location', APP_TD); ?>" />
                    </label>

                    <input type="text" class="text" name="jr_address" id="geolocation-address" value="<?php if (isset($posted['jr_address'])) echo esc_attr($posted['jr_address']); ?>" />
                    <input type="hidden" class="text" name="jr_geo_latitude" id="geolocation-latitude" value="<?php if (isset($posted['jr_geo_latitude'])) echo esc_attr($posted['jr_geo_latitude']); ?>" />
                    <input type="hidden" class="text" name="jr_geo_longitude" id="geolocation-longitude" value="<?php if (isset($posted['jr_geo_longitude'])) echo esc_attr($posted['jr_geo_longitude']); ?>" />
                </p>

                <div id="map_wrap" style="border:solid 2px #ddd;"><div id="geolocation-map" style="width:100%;height:300px;"></div></div>
            </div>

        </fieldset>
        
        <fieldset style="display:none">
            <legend><?php _e('Education', APP_TD); ?></legend>
            <p><?php _e('Detail your education, including details on your qualifications and schools/universities attended. Please include all your volunteer work, certificates and additional info', APP_TD); ?></p>
            <p class="education">
                <?php if ('yes' == get_option('jr_html_allowed') && !wp_is_mobile()) { ?>
                    <?php wp_editor($posted['education'], 'education', jr_get_editor_settings()); ?>
                <?php } else { ?>
                    <textarea id="education" name="education" cols="30" rows="5" ><?php if (isset($posted['education'])) echo esc_textarea($posted['education']); ?></textarea>
                <?php } ?>
            </p>
        </fieldset>
        
        <fieldset>
            <legend><?php _e('Overall Average, Last Year of Studies') ?></legend>

            <?php
            if (isset($posted['overall_average']) &&
                    $posted['overall_average'] == 'below 70%') {
                ?>
                <input checked="checked" type="radio" name="overall_average" value="below 70%" />
                <?} else {?>
                <input type="radio" name="overall_average" value="below 70%" />
            <?php } ?>
            <label class="overall_average">Below 70%</label>
            <?php
            if (isset($posted['overall_average']) &&
                    $posted['overall_average'] == '70% - 80%') {
                ?>

                <input checked="checked" type="radio" name="overall_average" value="70% - 80%" />

                <?} else {?>
                <input type="radio" name="overall_average" value="70% - 80%" />
            <?php } ?>
            <label class="overall_average">70% - 80%</label>

            <?php
            if (isset($posted['overall_average']) &&
                    $posted['overall_average'] == '80% - 90%') {
                ?>

                <input checked="checked" type="radio" name="overall_average" value="80% - 90%" />
    <?php } else { ?>
                <input type="radio" name="overall_average" value="80% - 90%" />
            <?php } ?>
            <label class="overall_average">80% - 90%</label>

    <?php
    if (isset($posted['overall_average']) &&
            $posted['overall_average'] == '90% - 95%') {
        ?>

                <input checked="checked" type="radio" name="overall_average" value="90% - 95%" />

            <?php } else { ?>
                <input type="radio" name="overall_average" value="90% - 95%" />
            <?php } ?>
            <label class="overall_average">90% - 95%</label>

            <?php
            if (isset($posted['overall_average']) &&
                    $posted['overall_average'] == '95% - 100%') {
                ?>

                <input checked="checked" type="radio" name="overall_average" value="95% - 100%" />
            <?php } else { ?>
                <input type="radio" name="overall_average" value="95% - 100%" />
            <?php } ?>
            <label class="overall_average">95% - 100%</label>
            <br />
            <label>&nbsp;&nbsp;I have transcripts</label>
    <?php if (isset($posted['transcripts']) && $posted['transcripts'] == 'Yes') { ?>
                <input checked="checked" type="checkbox" name="transcripts" value="Yes" />
    <?php } else { ?>
                <input type="checkbox" name="transcripts" value="Yes" />
    <?php } ?>
            <br />
            <p class="optional"><label for="degree"><?php _e('Degree', APP_TD); ?></label> <input type="text" class="text" name="degree" id="degree" value="<?php if (isset($posted['degree'])) echo $posted['degree']; ?>" /></p>

            <p class="optional"><label for="institution"><?php _e('Institution', APP_TD); ?></label> <input type="text" class="text" name="institution" id="institution" value="<?php if (isset($posted['institution'])) echo $posted['institution']; ?>" /></p>

            <p class="optional">
                <label for="degree_date_issued"><?php _e('Year Issued', APP_TD); ?></label>
                <input class="text" type="text" name="degree_date_issued" value="<?php if (isset($posted['degree_date_issued'])) echo $posted['degree_date_issued']; ?>"
                       id="degree_date_issued" placeholder="<?php _e('Year Issued', APP_TD); ?>" />   
            </p>


        </fieldset>

        <fieldset>
            <legend><?php _e('Skills', APP_TD); ?></legend>

                                <!--p class="optional"><label for="skills"><?php _e('Separate with a comma <small>(one per line)</small>', APP_TD); ?></label> <textarea rows="1" cols="30" name="skills" id="skills" class="short grow" placeholder="<?php _e('e.g. XHTML (5 years experience)', APP_TD); ?>"><?php if (isset($posted['skills'])) echo $posted['skills']; ?></textarea></p-->

            <p class="optional"><label for="specialities"><?php _e('Separated with a comma e.g. AutoCAD Advanced, Flash basics, Typing 80 WPM, Simply Accounting Advanced', APP_TD); ?></label> <input type="text" class="tags text tag-input-commas" data-separator="," name="specialities" id="specialities" placeholder="<?php _e('e.g. Public Speaking, Team Management', APP_TD); ?>" value="<?php if (isset($posted['specialities'])) echo $posted['specialities']; ?>" /></p>

            <!--
                              <p class="optional"><label for="groups"><?php _e('Groups/Associations <small>e.g. IEEE, W3C</small>', APP_TD); ?></label> <input type="text" class="text text tag-input-commas" data-separator="," name="groups" value="<?php if (isset($posted['groups'])) echo $posted['groups']; ?>" id="groups" placeholder="<?php _e('e.g. IEEE, W3C', APP_TD); ?>" /></p>
            -->

            <!--
                              <p class="optional" id="languages_wrap"><label for="languages"><?php _e('Spoken Languages <small>e.g. English, French</small>', APP_TD); ?></label> <input type="text" class="text text tag-input-commas" data-separator="," name="languages" value="<?php if (isset($posted['languages'])) echo $posted['languages']; ?>" id="languages" placeholder="<?php _e('e.g. English, French', APP_TD); ?>" /></p>
            -->
        </fieldset>

        <fieldset style="display:none;">
            <legend><?php _e('Tests', APP_TD) ?></legend>
            <p class="optional"><label for="typing_test"><?php _e('Typing Test', APP_TD); ?></label> <input type="text" class="text" name="typing_test" value="<?php if (isset($posted['typing_test'])) echo $posted['typing_test']; ?>" id="typing_test" placeholder="<?php _e('Typing Test', APP_TD); ?>" /></p>

            <p class="optional"><label for="math_test"><?php _e('Math Test', APP_TD); ?></label> <input type="text" class="text" name="math_test" value="<?php if (isset($posted['math_test'])) echo $posted['math_test']; ?>" id="math_test" placeholder="<?php _e('Math Test', APP_TD); ?>" /></p>

            <p class="optional"><label for="english_test"><?php _e('English Test', APP_TD); ?></label> <input type="text" class="text" name="english_test" value="<?php if (isset($posted['english_test'])) echo $posted['english_test']; ?>" id="english_test" placeholder="<?php _e('English Test', APP_TD); ?>" /></p>

            <p class="optional"><label for="memory_test"><?php _e('Memory Test', APP_TD); ?></label> <input type="text" class="text" name="memory_test" value="<?php if (isset($posted['memory_test'])) echo $posted['memory_test']; ?>" id="memory_test" placeholder="<?php _e('Memory Test', APP_TD); ?>" /></p>


            <p class="optional"><label for="internet_speed"><?php _e('Internet Speed', APP_TD); ?></label> <input type="text" class="text" name="internet_speed" value="<?php if (isset($posted['internet_speed'])) echo $posted['internet_speed']; ?>" id="internet_speed" placeholder="<?php _e('Internet Speed', APP_TD); ?>" /></p>
        </fieldset>

        <fieldset>
            <legend><?php _e('Career Map', APP_TD) ?></legend>

            <!--Reference 1-->   
            <fieldset>
                <legend><?php _e('Most Recent Employment', APP_TD) ?></legend>

                <p class="optional"><label for="company_1_position"><?php _e('Position', APP_TD); ?></label> <input type="text" class="text" name="company_1_position" value="<?php if (isset($posted['company_1_position'])) echo $posted['company_1_position']; ?>" id="company_1_position" placeholder="<?php _e('Position', APP_TD); ?>" /></p>

                <p class="optional">
                    <label for="company_1_start_date"><?php _e('Start Date', APP_TD); ?></label>
                    <input class="text" type="date" name="company_1_start_date" value="<?php if (isset($posted['company_1_start_date'])) echo $posted['company_1_start_date']; ?>"
                           id="company_1_start_date" placeholder="<?php _e('Start Date', APP_TD); ?>" />   
                </p>

                <p class="optional">
                    <label for="company_1_end_date"><?php _e('End Date', APP_TD); ?></label>
                    <input class="text" type="date" name="company_1_end_date" value="<?php if (isset($posted['company_1_end_date'])) echo $posted['company_1_end_date']; ?>"
                           id="company_1_start_date" placeholder="<?php _e('Start Date', APP_TD); ?>" />   
                </p>

                        <?php
                        $job_types = array('Full-Time', 'Part-Time');
                        ?>

                <p class="optional">
                    <label for="company_1_job_type"><?php _e('Job Type', APP_TD); ?></label>
                    <select class="company_1_job_type" name="company_1_job_type"> 
                        <?php
                        foreach ($job_types as $types) {

                            if (isset($posted['company_1_job_type']) && $posted['company_1_job_type'] == $types) {
                                ?>
                                <option selected="selected"><?php echo $types; ?></option>

        <?php } else { ?>

                                <option><?php echo $types; ?></option>

        <?php } ?>
    <?php } ?>
                    </select>
                </p>

                <p class="optional"><label for="company_1_company"><?php _e('Company', APP_TD); ?></label> <input type="text" class="text" name="company_1_company" value="<?php if (isset($posted['company_1_company'])) echo $posted['company_1_company']; ?>" id="company_1_company" placeholder="<?php _e('Company', APP_TD); ?>" /></p>

                <p class="optional"><label for="company_1_city"><?php _e('City', APP_TD); ?></label> <input type="text" class="text" name="company_1_city" value="<?php if (isset($posted['company_1_city'])) echo $posted['company_1_city']; ?>" id="company_1_city" placeholder="<?php _e('City', APP_TD); ?>" /></p>

                <p class="optional"><label for="company_1_country"><?php _e('Country', APP_TD); ?></label> <input type="text" class="text" name="company_1_country" value="<?php if (isset($posted['company_1_country'])) echo $posted['company_1_country']; ?>" id="company_1_country" placeholder="<?php _e('Country', APP_TD); ?>" /></p>


                <?php
                $reasons_for_leaving = array(
                    "Career change",
                    "Career growth",
                    "Change in career path",
                    "Company cut backs",
                    "Company downsized",
                    "Company went out of business",
                    "Family circumstances",
                    "Family reasons",
                    "Flexible schedule",
                    "Getting married",
                    "Hours reduced",
                    "Job was outsourced",
                    "Good career opportunity",
                    "Good reputation and opportunity at the new company",
                    "Laid off",
                    "Landed a higher paying job",
                    "Limited growth at company",
                    "Long commute",
                    "Looking for a new challenge",
                    "Needed a full-time position",
                    "New challenge",
                    "Not compatible with company goals",
                    "Not enough hours",
                    "Not enough work or challenge",
                    "Offered a permanent position",
                    "Personal reasons",
                    "Position eliminated",
                    "Position ended",
                    "Relocating",
                    "Reorganization or merger",
                    "Retiring",
                    "Seasonal position",
                    "Seeking a challenge",
                    "Seeking more responsibility",
                    "Staying home to raise a family",
                    "Summer job",
                    "Temporary job",
                    "Travel",
                    "Went back to school",
                    "About to get fired",
                    "Arrested",
                    "Bad company to work for",
                    "Bored at work",
                    "Childcare issues",
                    "Didn't get along with co-workers",
                    "Didn't like the schedule",
                    "Didn't want to work as many hours",
                    "Didn't want to work evening or weekends",
                    "Hated my boss",
                    "Hated my job",
                    "Injured",
                    "Job was too difficult",
                    "Let go for harassment",
                    "Let go for tardiness",
                    "Manager was stupid",
                    "My boss was a jerk",
                    "My mom made me quit",
                    "No transportation",
                    "Overtime was required",
                    "Passed over for promotion",
                    "Rocky marriage");
                ?>

                <p class="optional">
                    <label for="company_1_reason_for_leaving"><?php _e('Reason for Leaving', APP_TD); ?></label>
                    <select class="company_1_reason_for_leaving" name="company_1_reason_for_leaving"> 
                        <?php
                        foreach ($reasons_for_leaving as $reasons) {

                            if (isset($posted['company_1_reason_for_leaving']) && $posted['company_1_reason_for_leaving'] == $reasons) {
                                ?>
                                <option selected="selected"><?php echo $reasons; ?></option>

        <?php } else { ?>

                                <option><?php echo $reasons; ?></option>

                    <?php } ?>
    <?php } ?>
                    </select>
                </p>

                        <?php
                        $salary_types = array('Per Month', 'Per Hour');
                        ?>

                <p class="optional">
                    <label for="company_1_salary_type"><?php _e('Salary Type', APP_TD); ?></label>
                    <select class="company_1_salary_type" name="company_1_salary_type"> 
                        <?php
                        foreach ($salary_types as $type) {

                            if (isset($posted['company_1_salary_type']) && $posted['company_1_salary_type'] == $type) {
                                ?>
                                <option selected="selected"><?php echo $type; ?></option>

        <?php } else { ?>

                                <option><?php echo $type; ?></option>

        <?php } ?>
    <?php } ?>
                    </select>
                </p>

                <p class="optional"><label for="company_1_starting_salary"><?php _e('Starting Salary', APP_TD); ?></label> <input type="text" class="text" name="company_1_starting_salary" value="<?php if (isset($posted['company_1_starting_salary'])) echo $posted['company_1_starting_salary']; ?>" id="company_1_starting_salary" placeholder="<?php _e('Per Month or Per Hour', APP_TD); ?>" /></p>

                <p class="optional"><label for="company_1_final_salary"><?php _e('Final Salary', APP_TD); ?></label> <input type="text" class="text" name="company_1_final_salary" value="<?php if (isset($posted['company_1_final_salary'])) echo $posted['company_1_final_salary']; ?>" id="company_1_final_salary" placeholder="<?php _e('Per Month or Per Hour', APP_TD); ?>" /></p>



                <p class="optional"><label for="reference_name_1"><?php _e('Reference Name', APP_TD); ?></label> <input type="text" class="text" name="reference_name_1" value="<?php if (isset($posted['reference_name_1'])) echo $posted['reference_name_1']; ?>" id="reference_name_1" placeholder="<?php _e('Reference Name', APP_TD); ?>" /></p>

                <p class="optional"><label for="reference_email_1"><?php _e('Reference Email', APP_TD); ?></label> <input type="text" class="text" name="reference_email_1" value="<?php if (isset($posted['reference_email_1'])) echo $posted['reference_email_1']; ?>" id="reference_email_1" placeholder="<?php _e('Reference Email', APP_TD); ?>" /></p>

                <p class="optional"><label for="reference_phone_number_1"><?php _e('Reference Phone Number', APP_TD); ?></label> <input type="text" class="text" name="reference_phone_number_1" value="<?php if (isset($posted['reference_phone_number_1'])) echo $posted['reference_phone_number_1']; ?>" id="reference_phone_number_1" placeholder="<?php _e('Reference Phone Number', APP_TD); ?>" /></p>

                <p class="optional"><label for="reference_position_1"><?php _e('Reference Position', APP_TD); ?></label> <input type="text" class="text" name="reference_position_1" value="<?php if (isset($posted['reference_position_1'])) echo $posted['reference_position_1']; ?>" id="reference_position_1" placeholder="<?php _e('Reference Position', APP_TD); ?>" /></p>

                <p class="optional"> 
                    <textarea style="width: 320px;" class="reference_additional_info_1" name="reference_additional_info_1" id="reference_additional_info_1" placeholder="<?php _e('Reference Additional Info', APP_TD); ?>" ><?php if (isset($posted['reference_additional_info_1'])) echo $posted['reference_additional_info_1']; ?></textarea></p>
            </fieldset>

            <!--Reference 2-->     

            <fieldset>
                <legend><?php _e('2nd Last Employment', APP_TD) ?></legend>

                <p class="optional"><label for="company_2_position"><?php _e('Position', APP_TD); ?></label> <input type="text" class="text" name="company_2_position" value="<?php if (isset($posted['company_2_position'])) echo $posted['company_2_position']; ?>" id="company_2_position" placeholder="<?php _e('Position', APP_TD); ?>" /></p>

                <p class="optional">
                    <label for="company_2_start_date"><?php _e('Start Date', APP_TD); ?></label>
                    <input class="text" type="date" name="company_2_start_date" value="<?php if (isset($posted['company_2_start_date'])) echo $posted['company_2_start_date']; ?>"
                           id="company_2_start_date" placeholder="<?php _e('Start Date', APP_TD); ?>" />   
                </p>

                <p class="optional">
                    <label for="company_2_end_date"><?php _e('End Date', APP_TD); ?></label>
                    <input class="text" type="date" name="company_2_end_date" value="<?php if (isset($posted['company_2_end_date'])) echo $posted['company_2_end_date']; ?>"
                           id="company_2_end_date" placeholder="<?php _e('Start Date', APP_TD); ?>" />   
                </p>


                <p class="optional">
                    <label for="company_2_job_type"><?php _e('Job Type', APP_TD); ?></label>
                    <select class="company_2_job_type" name="company_2_job_type"> 
                        <?php
                        foreach ($job_types as $types) {

                            if (isset($posted['company_2_job_type']) && $posted['company_2_job_type'] == $types) {
                                ?>
                                <option selected="selected"><?php echo $types; ?></option>

        <?php } else { ?>

                                <option><?php echo $types; ?></option>

        <?php } ?>
    <?php } ?>
                    </select>
                </p>


                <p class="optional"><label for="company_2_company"><?php _e('Company', APP_TD); ?></label> <input type="text" class="text" name="company_2_company" value="<?php if (isset($posted['company_2_company'])) echo $posted['company_2_company']; ?>" id="company_2_company" placeholder="<?php _e('Company', APP_TD); ?>" /></p>

                <p class="optional"><label for="company_2_city"><?php _e('City', APP_TD); ?></label> <input type="text" class="text" name="company_2_city" value="<?php if (isset($posted['company_2_city'])) echo $posted['company_2_city']; ?>" id="company_2_city" placeholder="<?php _e('City', APP_TD); ?>" /></p>

                <p class="optional"><label for="company_2_country"><?php _e('Country', APP_TD); ?></label> <input type="text" class="text" name="company_2_country" value="<?php if (isset($posted['company_2_country'])) echo $posted['company_2_country']; ?>" id="company_2_country" placeholder="<?php _e('Country', APP_TD); ?>" /></p>

                <p class="optional">
                    <label for="company_2_reason_for_leaving"><?php _e('Reason for Leaving', APP_TD); ?></label>
                    <select class="company_2_reason_for_leaving" name="company_2_reason_for_leaving"> 
                        <?php
                        foreach ($reasons_for_leaving as $reasons) {

                            if (isset($posted['company_2_reason_for_leaving']) && $posted['company_2_reason_for_leaving'] == $reasons) {
                                ?>
                                <option selected="selected"><?php echo $reasons; ?></option>

        <?php } else { ?>

                                <option><?php echo $reasons; ?></option>

        <?php } ?>
                        <?php } ?>
                    </select>
                </p>


                <p class="optional">
                    <label for="company_2_salary_type"><?php _e('Salary Type', APP_TD); ?></label>
                    <select class="company_2_salary_type" name="company_2_salary_type"> 
                        <?php
                        foreach ($salary_types as $type) {

                            if (isset($posted['company_2_salary_type']) && $posted['company_2_salary_type'] == $type) {
                                ?>
                                <option selected="selected"><?php echo $type; ?></option>

        <?php } else { ?>

                                <option><?php echo $type; ?></option>

        <?php } ?>
    <?php } ?>
                    </select>
                </p>

                <p class="optional"><label for="company_2_starting_salary"><?php _e('Starting Salary', APP_TD); ?></label> <input type="text" class="text" name="company_2_starting_salary" value="<?php if (isset($posted['company_2_starting_salary'])) echo $posted['company_2_starting_salary']; ?>" id="company_2_starting_salary" placeholder="<?php _e('Per Month or Per Hour', APP_TD); ?>" /></p>

                <p class="optional"><label for="company_2_final_salary"><?php _e('Final Salary', APP_TD); ?></label> <input type="text" class="text" name="company_2_final_salary" value="<?php if (isset($posted['company_2_final_salary'])) echo $posted['company_2_final_salary']; ?>" id="company_2_final_salary" placeholder="<?php _e('Per Month or Per Hour', APP_TD); ?>" /></p>


                <p class="optional"><label for="reference_name_2"><?php _e('Reference Name', APP_TD); ?></label> <input type="text" class="text" name="reference_name_2" value="<?php if (isset($posted['reference_name_2'])) echo $posted['reference_name_2']; ?>" id="reference_name_2" placeholder="<?php _e('Reference Name', APP_TD); ?>" /></p>

                <p class="optional"><label for="reference_email_2"><?php _e('Reference Email', APP_TD); ?></label> <input type="text" class="text" name="reference_email_2" value="<?php if (isset($posted['reference_email_2'])) echo $posted['reference_email_2']; ?>" id="reference_email_1" placeholder="<?php _e('Reference Email', APP_TD); ?>" /></p>

                <p class="optional"><label for="reference_phone_number_2"><?php _e('Reference Phone Number', APP_TD); ?></label> <input type="text" class="text" name="reference_phone_number_2" value="<?php if (isset($posted['reference_phone_number_2'])) echo $posted['reference_phone_number_2']; ?>" id="reference_phone_number_2" placeholder="<?php _e('Reference Phone Number', APP_TD); ?>" /></p>

                <p class="optional"><label for="reference_position_2"><?php _e('Reference Position', APP_TD); ?></label> <input type="text" class="text" name="reference_position_2" value="<?php if (isset($posted['reference_position_2'])) echo $posted['reference_position_2']; ?>" id="reference_position_2" placeholder="<?php _e('Reference Position', APP_TD); ?>" /></p>

                <p class="optional"> 
                    <textarea style="width: 320px;" class="reference_additional_info_2" name="reference_additional_info_2" id="reference_additional_info_2" placeholder="<?php _e('Reference Additional Info', APP_TD); ?>" ><?php if (isset($posted['reference_additional_info_2'])) echo $posted['reference_additional_info_2']; ?></textarea></p>

            </fieldset>


            <!--Reference 3-->     
            <fieldset>
                <legend><?php _e('3rd Last Employment', APP_TD) ?></legend>

                <p class="optional"><label for="company_3_position"><?php _e('Position', APP_TD); ?></label> <input type="text" class="text" name="company_3_position" value="<?php if (isset($posted['company_3_position'])) echo $posted['company_3_position']; ?>" id="company_3_position" placeholder="<?php _e('Position', APP_TD); ?>" /></p>

                <p class="optional">
                    <label for="company_3_start_date"><?php _e('Start Date', APP_TD); ?></label>
                    <input class="text" type="date" name="company_3_start_date" value="<?php if (isset($posted['company_3_start_date'])) echo $posted['company_3_start_date']; ?>"
                           id="company_3_start_date" placeholder="<?php _e('Start Date', APP_TD); ?>" />   
                </p>

                <p class="optional">
                    <label for="company_3_end_date"><?php _e('End Date', APP_TD); ?></label>
                    <input class="text" type="date" name="company_3_end_date" value="<?php if (isset($posted['company_3_end_date'])) echo $posted['company_3_end_date']; ?>"
                           id="company_3_end_date" placeholder="<?php _e('Start Date', APP_TD); ?>" />   
                </p>

                <p class="optional">
                    <label for="company_3_job_type"><?php _e('Job Type', APP_TD); ?></label>
                    <select class="company_3_job_type" name="company_3_job_type"> 
                        <?php
                        foreach ($job_types as $types) {

                            if (isset($posted['company_3_job_type']) && $posted['company_3_job_type'] == $types) {
                                ?>
                                <option selected="selected"><?php echo $types; ?></option>

        <?php } else { ?>

                                <option><?php echo $types; ?></option>

        <?php } ?>
    <?php } ?>
                    </select>
                </p>

                <p class="optional"><label for="company_3_company"><?php _e('Company', APP_TD); ?></label> <input type="text" class="text" name="company_3_company" value="<?php if (isset($posted['company_3_company'])) echo $posted['company_3_company']; ?>" id="company_3_company" placeholder="<?php _e('Company', APP_TD); ?>" /></p>

                <p class="optional"><label for="company_3_city"><?php _e('City', APP_TD); ?></label> <input type="text" class="text" name="company_3_city" value="<?php if (isset($posted['company_3_city'])) echo $posted['company_3_city']; ?>" id="company_3_city" placeholder="<?php _e('City', APP_TD); ?>" /></p>

                <p class="optional"><label for="company_3_country"><?php _e('Country', APP_TD); ?></label> <input type="text" class="text" name="company_3_country" value="<?php if (isset($posted['company_3_country'])) echo $posted['company_3_country']; ?>" id="company_3_country" placeholder="<?php _e('Country', APP_TD); ?>" /></p>

                <p class="optional">
                    <label for="company_3_reason_for_leaving"><?php _e('Reason for Leaving', APP_TD); ?></label>
                    <select class="company_3_reason_for_leaving" name="company_3_reason_for_leaving"> 
                        <?php
                        foreach ($reasons_for_leaving as $reasons) {

                            if (isset($posted['company_3_reason_for_leaving']) && $posted['company_3_reason_for_leaving'] == $reasons) {
                                ?>
                                <option selected="selected"><?php echo $reasons; ?></option>

        <?php } else { ?>

                                <option><?php echo $reasons; ?></option>

                            <?php } ?>
                        <?php } ?>
                    </select>
                </p>

                <p class="optional">
                    <label for="company_3_salary_type"><?php _e('Salary Type', APP_TD); ?></label>
                    <select class="company_3_salary_type" name="company_3_salary_type"> 
                        <?php
                        foreach ($salary_types as $type) {

                            if (isset($posted['company_3_salary_type']) && $posted['company_3_salary_type'] == $type) {
                                ?>
                                <option selected="selected"><?php echo $type; ?></option>

        <?php } else { ?>

                                <option><?php echo $type; ?></option>

        <?php } ?>
    <?php } ?>
                    </select>
                </p>

                <p class="optional"><label for="company_3_starting_salary"><?php _e('Starting Salary', APP_TD); ?></label> <input type="text" class="text" name="company_3_starting_salary" value="<?php if (isset($posted['company_3_starting_salary'])) echo $posted['company_3_starting_salary']; ?>" id="company_3_starting_salary" placeholder="<?php _e('Per Month or Per Hour', APP_TD); ?>" /></p>

                <p class="optional"><label for="company_3_final_salary"><?php _e('Final Salary', APP_TD); ?></label> <input type="text" class="text" name="company_3_final_salary" value="<?php if (isset($posted['company_3_final_salary'])) echo $posted['company_3_final_salary']; ?>" id="company_3_final_salary" placeholder="<?php _e('Per Month or Per Hour', APP_TD); ?>" /></p>

                <p class="optional"><label for="reference_name_3"><?php _e('Reference Name', APP_TD); ?></label> <input type="text" class="text" name="reference_name_3" value="<?php if (isset($posted['reference_name_3'])) echo $posted['reference_name_3']; ?>" id="reference_name_3" placeholder="<?php _e('Reference Name', APP_TD); ?>" /></p>

                <p class="optional"><label for="reference_email_3"><?php _e('Reference Email', APP_TD); ?></label> <input type="text" class="text" name="reference_email_3" value="<?php if (isset($posted['reference_email_3'])) echo $posted['reference_email_3']; ?>" id="reference_email_3" placeholder="<?php _e('Reference Email', APP_TD); ?>" /></p>

                <p class="optional"><label for="reference_phone_number_3"><?php _e('Reference Phone Number', APP_TD); ?></label> <input type="text" class="text" name="reference_phone_number_3" value="<?php if (isset($posted['reference_phone_number_3'])) echo $posted['reference_phone_number_3']; ?>" id="reference_phone_number_3" placeholder="<?php _e('Reference Phone Number', APP_TD); ?>" /></p>

                <p class="optional"><label for="reference_position_3"><?php _e('Reference Position', APP_TD); ?></label> <input type="text" class="text" name="reference_position_3" value="<?php if (isset($posted['reference_position_3'])) echo $posted['reference_position_3']; ?>" id="reference_position_3" placeholder="<?php _e('Reference Position', APP_TD); ?>" /></p>

                <p class="optional"> 
                    <textarea style="width: 320px;" class="reference_additional_info_3" name="reference_additional_info_3" id="reference_additional_info_3" placeholder="<?php _e('Reference Additional Info', APP_TD); ?>" ><?php if (isset($posted['reference_additional_info_3'])) echo $posted['reference_additional_info_3']; ?></textarea></p>
            </fieldset>
            <!--
            <fieldset>
                <legend><?php _e('Other Employments', APP_TD); ?></legend>
                <p class="other_employments">
    <?php if ('yes' == get_option('jr_html_allowed') && !wp_is_mobile()) { ?>
        <?php wp_editor($posted['other_employments'], 'other_employments', jr_get_editor_settings()); ?>
    <?php } else { ?>
                        <textarea id="other_employments" name="other_employments" cols="30" rows="5" ><?php if (isset($posted['other_employments'])) echo esc_textarea($posted['other_employments']); ?></textarea>
    <?php } ?>
                </p>
            </fieldset> 
            -->
        </fieldset>

        <fieldset>
            <!--
            <iframe id="widget" type="text/html" width="640" height="390"
                              src="https://www.youtube.com/upload_embed" frameborder="0"></iframe>
            -->

            <div id="widget"></div>
            <div id="player"></div>
            <div id="video_link">
                <label>Interview Video Link</label>
                <input id="interview_video" style="width: 640px;" name="interview_video" type="text" value="<?php if (isset($posted['interview_video'])) echo $posted['interview_video']; ?>"/>
            </div>	


            <script type="text/javascript">
                var tag = document.createElement('script');

                tag.src = "https://www.youtube.com/iframe_api";
                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                var player1;

                function onYouTubeIframeAPIReady() {

                    new YT.UploadWidget('widget', {
                        events: {
                            onStateChange: onWidgetStateChange,
                            onUploadSuccess: onUploadSuccess,
                            onProcessingComplete: onProcessingComplete
                        }

                    });
                }

                function onWidgetStateChange(event) {
                    if (event.data.state == YT.UploadWidgetState.RECORDING) {

                    }

                }

                function onUploadSuccess(event) {
                    var text = document.getElementById('interview_video');
                    text.value = "https://www.youtube.com/embed/" + event.data.videoId;
                }

                function onProcessingComplete(event) {
                    /*  
                     new YT.Player('player', {
                     height: 300,
                     width: 450,
                     videoId: event.data.videoId,
                     });*/
                    /*var text = document.getElementById('interview_video');
                     text.value = "https://www.youtube.com/embed/" + event.data.videoId;*/

                }
            </script>

            <div id="video-interview-questions">  
                <label>Video Interview Instructions</label>

                <ul>
                    <li>You cannot stop and start the video without losing your previous recording.</li>
                    <li>Once you click “allow” on the popup window the video will start.</li>
                    <li>Once you have finished, click “upload”.</li>
                    <li>It takes a few minutes to see the video due to processing.</li>
                    <li>If you are having issues, try another browser.</li>
                    <li>You can also record on your computer, upload to Youtube then paste the link above.</li>
                </ul>
                <label>Video Interview Questions</label>

                <ol>
                    <li>Why did you choose this line of work?</li>
                    <li>What do you do in your spare time?</li>
                    <li>What are your greatest strengths?</li>
                    <li>What are you greatest weaknesses?</li>
                    <li>Do you have any health or personal issues that may affect job performance?</li>
                    <li>In your last job how many sick days did you take off?</li>
                    <li>Why should we hire you?</li>
                </ol>

                </table>
            </div>
        </fieldset>


        <p><input type="submit" class="submit" name="save_resume" value="<?php _e('Save &rarr;', APP_TD); ?>" /></p>

        <div class="clear"></div>

    </form>
    <script type="text/javascript">

        jQuery(function () {

            /* Auto Complete */
            var availableTags = [
    <?php
    $terms_array = array();
    $terms = get_terms('resume_languages', 'hide_empty=0');
    if ($terms)
        foreach ($terms as $term) {
            $terms_array[] = '"' . $term->name . '"';
        }
    echo implode(',', $terms_array);
    ?>
            ];
            function split(val) {
                return val.split(/,\s*/);
            }
            function extractLast(term) {
                return split(term).pop();
            }
            jQuery("#languages_wrap input").on("keydown", function (event) {
                if ((event.keyCode === jQuery.ui.keyCode.TAB || event.keyCode === jQuery.ui.keyCode.COMMA) &&
                        jQuery(this).data("autocomplete").menu.active) {
                    event.preventDefault();
                }
            }).autocomplete({
                minLength: 0,
                source: function (request, response) {
                    // delegate back to autocomplete, but extract the last term
                    response(jQuery.ui.autocomplete.filter(
                            availableTags, extractLast(request.term)));
                },
                focus: function () {
                    jQuery('input.ui-autocomplete-input').val('');
                    // prevent value inserted on focus
                    return false;
                },
                select: function (event, ui) {

                    var terms = split(this.value);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    //this.value = terms.join( ", " );
                    this.value = terms.join("");

                    jQuery(this).blur();
                    jQuery(this).focus();

                    return false;
                }
            });

        });
    </script>
    <?php
}
