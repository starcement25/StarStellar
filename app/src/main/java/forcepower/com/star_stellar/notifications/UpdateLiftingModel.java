package forcepower.com.star_stellar.notifications;

public class UpdateLiftingModel {
    public String getEid() {
        return eid;
    }

    public void setEid(final String eid) {
        this.eid = eid;
    }

    public String getE_name() {
        return e_name;
    }

    public void setE_name(final String e_name) {
        this.e_name = e_name;
    }

    public String getE_mobile() {
        return e_mobile;
    }

    public void setE_mobile(final String e_mobile) {
        this.e_mobile = e_mobile;
    }

    public String getE_city_town() {
        return e_city_town;
    }

    public void setE_city_town(final String e_city_town) {
        this.e_city_town = e_city_town;
    }

    public String getE_profile_image_url() {
        return e_profile_image_url;
    }

    public void setE_profile_image_url(final String e_profile_image_url) {
        this.e_profile_image_url = e_profile_image_url;
    }

    public String get_json_row() {
        return json_row;
    }

    public void set_json_row(final String val) {
        this.json_row = val;
    }

    private String eid, e_name, e_mobile, e_city_town, e_profile_image_url, json_row;
}
