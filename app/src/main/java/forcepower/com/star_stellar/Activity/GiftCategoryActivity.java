package forcepower.com.star_stellar.Activity;

import static forcepower.com.star_stellar.Class.AllUrl.category_list;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.EngGiftsActivity;
import forcepower.com.star_stellar.Activity.TE.TeGiftsActivity;
import forcepower.com.star_stellar.R;
import forcepower.com.star_stellar.Class.GiftCategory;
import forcepower.com.star_stellar.Class.GiftCategoryAdapter;

public class GiftCategoryActivity extends AppCompatActivity {

    private GiftCategoryAdapter adapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_gift_category);

        RecyclerView rvCategories = findViewById(R.id.rvCategories);
        final ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
        ivBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
            }
        });

        adapter = new GiftCategoryAdapter(category -> {
           if(category.getItemCount() > 0){
               Boolean bool = getIntent().getBooleanExtra("isTE", false);
               Intent intent;
               if(bool){
                   intent = new Intent(this, TeGiftsActivity.class);
               }else {
                   intent = new Intent(this, EngGiftsActivity.class);
               }
               intent.putExtra("category_id",category.getId());
               startActivity(intent);
           }else {
               Toast.makeText(this, "No Gift under this category", Toast.LENGTH_SHORT).show();
           }
        });

        rvCategories.setLayoutManager(new GridLayoutManager(this, 2));
        rvCategories.setAdapter(adapter);

        fetchGiftCategories();
    }

    public void fetchGiftCategories() {
        try {
            String url = category_list;

            AsyncHttpClient client = new AsyncHttpClient();
            client.setTimeout(DEFAULT_TIMEOUT);

            print_Log_d("category_U ", url);

            client.get(url, new AsyncHttpResponseHandler() {

                @Override
                public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {

                    String str = new String(responseBody);

                    try {
                        print_Log_d("gift_R ", str);

                        JSONObject reader = new JSONObject(str);

                        if (reader.getString("process_status").equalsIgnoreCase("YES")) {

                            JSONArray array = reader.getJSONArray("category_data");

                            List<GiftCategory> list = new ArrayList<>();

                            for (int i = 0; i < array.length(); i++) {

                                JSONObject obj = array.getJSONObject(i);

                                String id = obj.optString("category_id");
                                String name = obj.optString("category_name");
                                int count = obj.optInt("gift_count");

                                list.add(new GiftCategory(id, name, null, count));
                            }

                            adapter.submitList(list);
                        }

                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }

                @Override
                public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                    print_Log_d("category_ERR ", error != null ? error.toString() : "Unknown error");
                }
            });

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}