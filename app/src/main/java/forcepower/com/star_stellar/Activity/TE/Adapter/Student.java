package forcepower.com.star_stellar.Activity.TE.Adapter;

import java.io.Serializable;

public class Student implements Serializable {

    private static final long serialVersionUID = 1L;

    private String name, emailId, status, _json_row, r_recomended_by;

    public Student(String name, String emailId, String status, String _json_row, String r_recomended_by) {
        this.name = name;
        this.emailId = emailId;
        this._json_row = _json_row;
        this.status = status;
        this.r_recomended_by = r_recomended_by;
    }

    public String getName() {
        return name;
    }

    public String get_json_row() {
        return _json_row;
    }

    public void set_json_row(String _json_row) {
        this._json_row = _json_row;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getEmailId() {
        return emailId;
    }

    public String get_r_recomended_by() {
        return r_recomended_by;
    }

    public void setEmailId(String emailId) {
        this.emailId = emailId;
    }
}