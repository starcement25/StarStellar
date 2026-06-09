package forcepower.com.star_stellar.Activity.Engineer;

import java.io.Serializable;

public class ExistingSiteModel implements Serializable {
    private String r_site_id, r_site_name, r_contact_person_name,
            r_mobile_no, r_address, r_site_potential_in_mt,
            r_contact_person_category_name, r_recomended_site_image_url,
            r_status, r_submission_date, r_submission_date_modified,
            expected_product_id, expected_product_name, expected_consumption, json_row;

    public String get_r_site_id() {
        return r_site_id;
    }

    public void set_r_site_id(final String r_site_id) {
        this.r_site_id = r_site_id;
    }

    public String get_r_site_name() {
        return r_site_name;
    }

    public void set_r_site_name(final String r_site_name) {
        this.r_site_name = r_site_name;
    }

    public String get_r_contact_person_name() {
        return r_contact_person_name;
    }

    public void set_r_contact_person_name(final String r_contact_person_name) {
        this.r_contact_person_name = r_contact_person_name;
    }

    public String get_r_mobile_no() {
        return r_mobile_no;
    }

    public void set_r_mobile_no(final String r_mobile_no) {
        this.r_mobile_no = r_mobile_no;
    }

    public String get_r_address() {
        return r_address;
    }

    public void set_r_address(final String r_address) {
        this.r_address = r_address;
    }

    public String get_r_site_potential_in_mt() {
        return r_site_potential_in_mt;
    }

    public void set_r_site_potential_in_mt(final String r_site_potential_in_mt) {
        this.r_site_potential_in_mt = r_site_potential_in_mt;
    }

    public String get_r_contact_person_category_name() {
        return r_contact_person_category_name;
    }

    public void set_r_contact_person_category_name(final String r_contact_person_category_name) {
        this.r_contact_person_category_name = r_contact_person_category_name;
    }

    public String get_r_recomended_site_image_url() {
        return r_recomended_site_image_url;
    }

    public void set_r_recomended_site_image_url(final String r_recomended_site_image_url) {
        this.r_recomended_site_image_url = r_recomended_site_image_url;
    }

    public String get_r_status() {
        return r_status;
    }

    public void set_r_status(final String r_status) {
        this.r_status = r_status;
    }

    public String get_r_submission_date() {
        return r_submission_date;
    }

    public void set_r_submission_date(final String r_submission_date) {
        this.r_submission_date = r_submission_date;
    }

    public String get_r_submission_date_modified() {
        return r_submission_date_modified;
    }

    public void set_r_submission_date_modified(final String r_submission_date_modified) {
        this.r_submission_date_modified = r_submission_date_modified;
    }

    public String getExpected_product_id() {
        return expected_product_id;
    }

    public void setExpected_product_id(final String expected_product_id) {
        this.expected_product_id = expected_product_id;
    }

    public String getExpected_product_name() {
        return expected_product_name;
    }

    public void setExpected_product_name(final String expected_product_name) {
        this.expected_product_name = expected_product_name;
    }

    public String getExpected_consumption() {
        return expected_consumption;
    }

    public void setExpected_consumption(final String expected_consumption) {
        this.expected_consumption = expected_consumption;
    }

    public String get_json_row() {
        return json_row;
    }

    public void set_json_row(final String val) {
        this.json_row = val;
    }
}
