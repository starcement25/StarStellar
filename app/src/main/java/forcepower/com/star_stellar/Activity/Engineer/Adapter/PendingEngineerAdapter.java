package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONObject;

import java.util.ArrayList;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.PendingEngineerActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.AllUrl.ws_approve_reject_the_engineer_for_te;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_TE_code;


public class PendingEngineerAdapter extends BaseAdapter {

    private Activity myActivity;
    private ArrayList<CommonHelper> menu_item_list = new ArrayList<>();

    public PendingEngineerAdapter(final Activity myActivity, final ArrayList<CommonHelper> menu_item_list_) {
        this.myActivity = myActivity;
        this.menu_item_list = menu_item_list_;
    }

    @Override
    public int getCount() {
        return menu_item_list.size();
    }

    @Override
    public CommonHelper getItem(int position) {

        return menu_item_list.get(position);
    }

    @Override
    public long getItemId(int position) {

        return 0;
    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {
        try {
            ViewHolder holder = new ViewHolder();
            if (convertView == null) {
                final LayoutInflater mInflater = (LayoutInflater) myActivity.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);
                convertView = mInflater.inflate(R.layout.list_item_pending_engineer, null);
                holder = new ViewHolder();

                holder.tv_e_name = (TextView) convertView.findViewById(R.id.tv_e_name);
                holder.tv_e_mobile = (TextView) convertView.findViewById(R.id.tv_e_mobile);
                holder.tv_pending_approve = (TextView) convertView.findViewById(R.id.tv_pending_approve);
                holder.tv_pending_reject = (TextView) convertView.findViewById(R.id.tv_pending_reject);
                holder.iv_pending_engg_dp = (ImageView) convertView.findViewById(R.id.iv_pending_engg_dp);

                convertView.setTag(holder);
            } else {
                holder = (ViewHolder) convertView.getTag();
            }

            holder.tv_e_name.setText(menu_item_list.get(position).getItem3());
            holder.tv_e_mobile.setText("Mobile : " + menu_item_list.get(position).getItem2());

            holder.tv_pending_approve.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    if (isInternetConnected(myActivity)) {
                        branch_selection_Approve("APPROVE", menu_item_list.get(position).getItem0());
                    } else {
                        Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                    }
                }
            });
            holder.tv_pending_reject.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    reject_confirm_dialog(menu_item_list.get(position).getItem0());
                }
            });

            //e_profile_image_url
            Glide.with(myActivity).load(menu_item_list.get(position).getItem4())
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .dontAnimate()
                    .error(R.drawable.en_profile)
                    .into(holder.iv_pending_engg_dp);
        } catch (final Exception e) {
            e.printStackTrace();
        }
        return convertView;
    }

    public class ViewHolder {
        TextView tv_e_name, tv_e_mobile, tv_pending_approve, tv_pending_reject;
        ImageView iv_pending_engg_dp;
    }

    private void reject_confirm_dialog(final String eid) {
        try {
            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);
            builder.setMessage("Are you want to reject?");

            builder.setNegativeButton("No", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    //
                }
            });
            builder.setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    if (isInternetConnected(myActivity)) {
                        update_pending_engineer("REJECT", eid, "");
                    } else {
                        Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                    }
                }
            });
            AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void branch_selection_Approve(final String status, final String eid) {
        try {
            //for Runtime permission
            final ArrayList<CommonHelper> p_list = ((PendingEngineerActivity) myActivity).returnVal();
            String[] data = new String[p_list.size()];
            for (int i = 0; i < p_list.size(); i++) {
                data[i] = p_list.get(i).getItem1();
            }
            final CharSequence[] items = data;

            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);

            final TextView tvCPopup = new TextView(myActivity);
            tvCPopup.setText("Please select the Branch");
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(myActivity.getResources().getColor(R.color.white));
            tvCPopup.setTextSize(12);
            tvCPopup.setBackgroundColor(myActivity.getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin * 2, 0, margin * 2);
            builder.setCustomTitle(tvCPopup);

            builder.setItems(items, new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int index) {

                    update_pending_engineer(status, eid, p_list.get(index).getItem0());
                }
            }).setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    //
                }
            });
            builder.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void update_pending_engineer(String status, String eid, final String selected_branch) {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("te_code", get_TE_code(myActivity));
        params.put("eid", eid);
        params.put("status", status);
        params.put("selected_branch", selected_branch); //vola2715

        print_Log_d("ws_approve_reject_the_engineer_for_te_URL ", ws_approve_reject_the_engineer_for_te);
        print_Log_d("ws_approve_reject_the_engineer_for_te_PARAM ", params.toString());

		/*
		te_code,eid,status

		Note: All fields are mandatory.
		status = APPROVE or REJECT

		 */
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_approve_reject_the_engineer_for_te, params, new AsyncHttpResponseHandler() //vola2715
        {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("ws_approve_reject_the_engineer_for_te_STR ", str);

                    Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        myActivity.finish();
                    }
                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
            }


        });
    }

    ProgressDialog progressDialogObj;

    public void loadDialog() {
        if (progressDialogObj != null && progressDialogObj.isShowing())
            progressDialogObj.dismiss();
        progressDialogObj = new ProgressDialog(myActivity);
        progressDialogObj.setCancelable(false);
        progressDialogObj.show();
    }

    public void dismissDialog() {
        if (progressDialogObj != null && progressDialogObj.isShowing())
            progressDialogObj.dismiss();
    }
}
