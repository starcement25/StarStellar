package forcepower.com.star_stellar.Activity.Engineer;

import android.app.Activity;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;


public class NotificationDetailsActivity extends BaseActivity {
    Activity myActivity;
    String title = "", notice_message = "", image_url = "";
    TextView tvNotiTitle, tvNotiMessage;
    ImageView imNotice;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_notification_details);

        myActivity = NotificationDetailsActivity.this;

        //Header_View
        RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
        rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
        LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
        llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
        llTopView.setPadding(0, 0, 0, 0);
        RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
        rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
        TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
        tvCaption.setText("Notification");
        ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
        ivBack.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onBackPressed();
            }
        });

        try {
            tvNotiTitle = (TextView) findViewById(R.id.tvNotiTitle);
            tvNotiMessage = (TextView) findViewById(R.id.tvNotiMessage);
            imNotice = (ImageView) findViewById(R.id.imNotice);


            Bundle extras = getIntent().getExtras();
            if (extras != null) {
                title = extras.getString("title") + "";
                notice_message = extras.getString("message") + "";
                tvNotiTitle.setText(title);
                tvNotiMessage.setText(notice_message);


                image_url = extras.getString("image") + "";
                if (!image_url.matches("")) {
                    Glide.with(this).load(image_url)
                            .diskCacheStrategy(DiskCacheStrategy.NONE)
                            .placeholder(R.drawable.default_)
                            .error(R.drawable.default_)
                            .into(imNotice);
                }
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }
}
