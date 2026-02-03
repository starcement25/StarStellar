package forcepower.com.star_stellar.Class;

public final class PlaceOrderModel {
    private String order_query_id;
    private String order_id;
    private String prod_name;
    private String qty_bags;

    public String getDate_and_time() {
        return date_and_time;
    }

    public void setDate_and_time(final String date_and_time) {
        this.date_and_time = date_and_time;
    }

    public String getOrder_id() {
        return order_id;
    }

    public void setOrder_id(final String order_id) {
        this.order_id = order_id;
    }

    public String getOrder_query_id() {
        return order_query_id;
    }

    public void setOrder_query_id(final String order_query_id) {
        this.order_query_id = order_query_id;
    }

    public String getProd_name() {
        return prod_name;
    }

    public void setProd_name(final String prod_name) {
        this.prod_name = prod_name;
    }

    public String getQty_bags() {
        return qty_bags;
    }

    public void setQty_bags(final String qty_bags) {
        this.qty_bags = qty_bags;
    }

    public String getQuery_date() {
        return query_date;
    }

    public void setQuery_date(final String query_date) {
        this.query_date = query_date;
    }

    public String getRssd_name() {
        return rssd_name;
    }

    public void setRssd_name(final String rssd_name) {
        this.rssd_name = rssd_name;
    }

    private String date_and_time;
    private String rssd_name;
    private String query_date;

    public String getDate_of_lifting() {
        return date_of_lifting;
    }

    public void setDate_of_lifting(final String date_of_lifting) {
        this.date_of_lifting = date_of_lifting;
    }

    public String getRemarks() {
        return remarks;
    }

    public void setRemarks(final String remarks) {
        this.remarks = remarks;
    }

    private String date_of_lifting, remarks;
    private String status_from_app;

    public String getStatus_from_app() {
        return status_from_app;
    }

    public void setStatus_from_app(final String rr) {
        this.status_from_app = rr;
    }

    public String getStatus_remarks() {
        return status_remarks;
    }

    public void setStatus_remarks(final String ff) {
        this.status_remarks = ff;
    }

    private String status_remarks;
}
