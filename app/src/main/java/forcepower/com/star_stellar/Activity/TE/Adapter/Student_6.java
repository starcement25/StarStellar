package forcepower.com.star_stellar.Activity.TE.Adapter;

import java.io.Serializable;

public class Student_6 implements Serializable {

    private static final long serialVersionUID = 1L;

    private String e_profile_image_url, e_name, eng_i_d, e_city_town, e_status, r_submission_date, e_mobile;

    public Student_6(String e_profile_image_url, String e_name,
                     String eng_i_d, String e_city_town, String e_status, String r_submission_date,
                     final String e_mobile) {
        this.e_profile_image_url = e_profile_image_url;
        this.e_name = e_name;
        this.e_status = e_status;
        this.e_city_town = e_city_town;
        this.eng_i_d = eng_i_d;
        this.r_submission_date = r_submission_date;
        this.e_mobile = e_mobile;
    }

    public String gete_profile_image_url() {
        return e_profile_image_url;
    }

    public String gete_status() {
        return e_status;
    }

    public String gete_name() {
        return e_name;
    }

    public String get_r_submission_date() {
        return r_submission_date;
    }

    public String get_ok_Eid() {
        return eng_i_d;
    }

    public String gete_city_town() {
        return e_city_town;
    }

    public void sete_name(String e_name) {
        this.e_name = e_name;
    }

    public String get_e_mobile() {
        return e_mobile;
    }

}
