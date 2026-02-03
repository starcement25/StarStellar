package forcepower.com.star_stellar.Activity.TE;

import static forcepower.com.star_stellar.Class.AllUrl.ws_show_approved_mapped_engineers_for_te;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_my_recommended_sites_for_engineer;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_TE_code;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.AdapterView;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.ExistingSiteListAdapter;
import forcepower.com.star_stellar.Activity.Engineer.ExistingSiteModel;
import forcepower.com.star_stellar.Activity.TE.Adapter.UpdateLiftingAdapter_New;
import forcepower.com.star_stellar.Activity.TE.Adapter.OnLoadMoreListener_MyEng;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.DividerItemDecoration;
import forcepower.com.star_stellar.R;
import forcepower.com.star_stellar.notifications.UpdateLiftingModel;

public class TeUpdateLiftingActivity extends BaseActivity {
    private Activity myActivity;
    private List<UpdateLiftingModel> p_list = new ArrayList<>();

    private UpdateLiftingAdapter_New pAdapter;
    protected Handler handler_p;
    private RecyclerView rv_Pending;
    private int page_no_E = 1;
    private final ArrayList<ExistingSiteModel> exisitng_site_list = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_te_update_lifting);

        try {
            myActivity = TeUpdateLiftingActivity.this;
            //Header_View
            final RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            final LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            final RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            final TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Update Lifting");
            final ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            //Grid
            rv_Pending = (RecyclerView) findViewById(R.id.rv_MyEngineer);
            rv_Pending.setHasFixedSize(true);
            rv_Pending.setLayoutManager(new LinearLayoutManager(myActivity));
            rv_Pending.addItemDecoration(new DividerItemDecoration(getResources().getDrawable(R.drawable.divider)));

            handler_p = new Handler();
            pAdapter = new UpdateLiftingAdapter_New(myActivity, p_list, rv_Pending);
            rv_Pending.setAdapter(pAdapter);

            if (isInternetConnected(myActivity)) {
                get_Engineer_details(1, "fresh_e");
                pAdapter.setOnLoadMoreListener(new OnLoadMoreListener_MyEng() {
                    @Override
                    public void onLoadMore() {
                        if (page_no_E > 0) {
                            p_list.add(null);
                            pAdapter.notifyItemInserted(p_list.size() - 1);

                            handler_p.postDelayed(new Runnable() {
                                @Override
                                public void run() {
                                    get_Engineer_details(page_no_E, "add_e");
                                }
                            }, 2000);
                        }
                    }
                });
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void get_Engineer_details(int page_no_E_, final String type) {
        if (!type.matches("add_e"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("te_code", get_TE_code(myActivity));
        params.put("page_no", page_no_E_ + "");
        params.put("search_term", "");

//te_code,page_no,search_term,status
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_approved_mapped_engineers_for_te, params, new AsyncHttpResponseHandler() {
            @SuppressLint("NotifyDataSetChanged")
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("ws_show_approved_mapped_engineers_for_te ", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        page_no_E++;
                        String pending_recommendation_data = reader.getString("engineer_data");
                        JSONArray ja = new JSONArray(pending_recommendation_data);
                        if (ja.length() > 0) {
                            if (type.matches("add_e")) {
                                p_list.remove(p_list.size() - 1);
                                pAdapter.notifyItemRemoved(p_list.size());
                            } else {
                                p_list.clear();
                            }

                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                final UpdateLiftingModel uL = new UpdateLiftingModel();
                                uL.setEid(e.getString("eid"));
                                uL.setE_name(e.getString("e_name"));
                                uL.setE_mobile(e.getString("e_mobile"));
                                uL.setE_city_town(e.getString("e_city_town"));
                                uL.setE_profile_image_url(e.getString("e_profile_image_url"));
                                uL.set_json_row(e.toString());

                                p_list.add(uL);

                                if (type.matches("add_e")) {
                                    pAdapter.notifyItemInserted(p_list.size());
                                }

                            }
                            if (!type.matches("add_e")) {
                                pAdapter.notifyDataSetChanged();
                            }
                            pAdapter.setLoaded();

                        }
                    } else {
                        page_no_E = -1;
                        if (type.matches("add_e")) {
                            p_list.remove(p_list.size() - 1);
                            pAdapter.notifyItemRemoved(p_list.size());
                        }
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

    @Override
    public void onResume() {
        super.onResume();
    }

    private ProgressDialog progressDialogObj;

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

    @Override
    public void onBackPressed() {
        finish();
    }

    public void getExistingSiteList() {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
//        params.put("mobile", mobile);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_my_recommended_sites_for_engineer, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("ws_show_my_recommended_sites_for_engineer ", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        final String my_recommended_site_data = reader.getString("my_recommended_site_data");
                        JSONArray ja = new JSONArray(my_recommended_site_data);
                        exisitng_site_list.clear();
                        for (int i = 0; i < ja.length(); i++) {
                            final JSONObject e = ja.getJSONObject(i);
                            final ExistingSiteModel existingSiteModel = new ExistingSiteModel();

                            existingSiteModel.set_r_site_id(e.optString("r_site_id"));
                            existingSiteModel.set_r_site_name(e.optString("r_site_name"));
                            existingSiteModel.set_r_contact_person_name(e.optString("r_contact_person_name"));
                            existingSiteModel.set_r_mobile_no(e.optString("r_mobile_no"));
                            existingSiteModel.set_r_address(e.optString("r_address"));
                            existingSiteModel.set_r_site_potential_in_mt(e.optString("r_site_potential_in_mt"));
                            existingSiteModel.set_r_contact_person_category_name(e.optString("r_contact_person_category_name"));
                            existingSiteModel.set_r_recomended_site_image_url(e.optString("r_recomended_site_image_url"));
                            existingSiteModel.set_r_status(e.optString("r_status"));
                            existingSiteModel.set_r_submission_date(e.optString("r_submission_date"));
                            existingSiteModel.set_r_submission_date_modified(e.optString("r_submission_date_modified"));
                            existingSiteModel.setExpected_product_id(e.optString("expected_product_id"));
                            existingSiteModel.setExpected_product_name(e.optString("expected_product_name"));
                            existingSiteModel.setExpected_consumption(e.optString("expected_consumption"));
                            existingSiteModel.set_json_row(e.toString());

                            exisitng_site_list.add(existingSiteModel);
                        }
                    }
                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();

                    if (exisitng_site_list.size() > 0) {
                        existing_Site_Dialog();
                    } else {
                        Toast.makeText(myActivity, "Not available", Toast.LENGTH_SHORT).show();
                    }
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
            }


        });
    }

    public void existing_Site_Dialog() {
        try {
            final AlertDialog.Builder issueBuilder = new AlertDialog.Builder(myActivity);

            final TextView tvCPopup = new TextView(myActivity);
            tvCPopup.setText("Existing Site List");
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(getResources().getColor(R.color.white));
            tvCPopup.setTextSize(12);
            tvCPopup.setBackgroundColor(getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin * 2, 0, margin * 2);
            issueBuilder.setCustomTitle(tvCPopup);

            // Get the layout inflater
            LayoutInflater inflater = myActivity.getLayoutInflater();
            // Inflate and set the layout for the dialog
            // Pass null as the parent view because its going in the dialog layout
            issueBuilder.setView(inflater.inflate(R.layout.dialog_list_view, null));

            final Dialog dialog = issueBuilder.create();

            dialog.setCanceledOnTouchOutside(true);
            dialog.setCancelable(true);
            dialog.show();

            final LinearLayout ll_m_search = (LinearLayout) dialog.findViewById(R.id.ll_m_search);
            ll_m_search.setVisibility(View.VISIBLE);
            final EditText et_m_search_bar = (EditText) dialog.findViewById(R.id.et_m_search_bar);

            final ImageView iv_m_search_clear = (ImageView) dialog.findViewById(R.id.iv_m_search_clear);
            iv_m_search_clear.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    et_m_search_bar.setText("");
                }
            });


            final ListView lv_dialog = (ListView) dialog.findViewById(R.id.lv_dialog);
            final ExistingSiteListAdapter eAdapter = new ExistingSiteListAdapter(myActivity, exisitng_site_list);
            lv_dialog.setAdapter(eAdapter);
            lv_dialog.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int index, long l) {
                    try {
                        Intent intent = new Intent(myActivity, TeRecommendationDetails_UL.class);
                        intent.putExtra("recommended_site_details", exisitng_site_list.get(index).get_json_row());//json_row
                        myActivity.startActivity(intent);
                    } catch (Exception e) {
                        e.printStackTrace();
                    } finally {
                        dialog.dismiss();
                    }
                }
            });

            et_m_search_bar.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

                }

                @Override
                public void onTextChanged(CharSequence cs, int i, int i1, int i2) {

                    final ArrayList<ExistingSiteModel> tmp_exisitng_site_list = new ArrayList<>();

                    for (int index = 0; index < exisitng_site_list.size(); index++) {
                        if (exisitng_site_list.get(index).get_r_site_name().toLowerCase().contains(cs.toString().toLowerCase().trim()) ||
                                exisitng_site_list.get(index).get_r_mobile_no().toLowerCase().contains(cs.toString().toLowerCase().trim())) {
                            tmp_exisitng_site_list.add(exisitng_site_list.get(index));
                        }
                    }

                    eAdapter.setFilter(tmp_exisitng_site_list);
                }

                @Override
                public void afterTextChanged(Editable editable) {

                }
            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }
}
