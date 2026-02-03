package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.annotation.TargetApi;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.graphics.pdf.PdfRenderer;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.os.ParcelFileDescriptor;
import android.text.Html;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.content.FileProvider;

import org.json.JSONObject;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import forcepower.com.star_stellar.BuildConfig;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.OnSwipeTouchListener;
import forcepower.com.star_stellar.Class.TouchImageView;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class NotificationPdfActivity extends BaseActivity {
    Activity myActivity;
    TouchImageView ImageView_community_resource_document;
    int minValuePdfPage = 0;
    int maxValuePdfPage = 0;
    int currentPdfPage = 0;
    int totalPage = 1;
    PdfRenderer renderer;
    File pdfFile = new File("");
    int currentApiVersion = Build.VERSION.SDK_INT;
    TextView pdfPageNumberTextView, vertical;
    String filePath = "", id = "", title = "", imageUrl = "", message = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_notification_pdf);
        try {
            myActivity = NotificationPdfActivity.this;

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

            Bundle extras = getIntent().getExtras();
            if (extras != null) {
                id = extras.getString("id") + "";
                title = extras.getString("title") + "";
                imageUrl = extras.getString("imageUrl") + "";
                message = extras.getString("message") + "";
            }

            filePath = "/mnt/sdcard/OTP/" + id + ".pdf";
            pdfFile = new File(filePath);

            if (pdfFile.exists() && !filePath.matches("")) {
                loadFromLocal();
            } else {
                if (isInternetConnected(myActivity)) {
                    new getScheme_Asynctask(myActivity).execute();
                } else {
                    Toast.makeText(myActivity, "You need to have an active internet connection to use this feature.", Toast.LENGTH_SHORT).show();
                }
            }

        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @TargetApi(Build.VERSION_CODES.LOLLIPOP)
    private void ShowPdfPagesInImageView(int i) {
        try {
            renderer = new PdfRenderer(ParcelFileDescriptor.open(pdfFile, ParcelFileDescriptor.MODE_READ_ONLY));
            PdfRenderer.Page page = renderer.openPage(i);
            Bitmap mBitmap = Bitmap.createBitmap(page.getWidth(), page.getHeight(), Bitmap.Config.ARGB_4444);
            // say we render for showing on the screen
            page.render(mBitmap, null, null, PdfRenderer.Page.RENDER_MODE_FOR_DISPLAY);

            // do stuff with the bitmap
            ImageView_community_resource_document.setImageBitmap(mBitmap);
            // close the page
            page.close();

            // close the renderer
            renderer.close();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @TargetApi(Build.VERSION_CODES.LOLLIPOP)
    private void readPdfFileAndShowOnImageView() {
        try {

            if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP) {
                renderer = new PdfRenderer(ParcelFileDescriptor.open(pdfFile, ParcelFileDescriptor.MODE_READ_ONLY));
                // let us just render all pages
                final int pageCount = renderer.getPageCount();
                if (pageCount > 0) {
                    maxValuePdfPage = pageCount - 1;
                    totalPage = pageCount;
                    pdfPageNumberTextView = (TextView) findViewById(R.id.pdfPageNumberTextView);
                    pdfPageNumberTextView.setVisibility(View.VISIBLE);
//                    pdfPageNumberTextView.setText(1+"/"+pageCount);
                    pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + 1 + "/<font color='#000000'>" + totalPage + "</font>"));
                }

                PdfRenderer.Page page = renderer.openPage(0);
                Bitmap mBitmap = Bitmap.createBitmap(page.getWidth(), page.getHeight(), Bitmap.Config.ARGB_4444);
                // say we render for showing on the screen
                page.render(mBitmap, null, null, PdfRenderer.Page.RENDER_MODE_FOR_DISPLAY);

                // do stuff with the bitmap
                ImageView_community_resource_document.setImageBitmap(mBitmap);
                // close the page
                page.close();

                // close the renderer
                renderer.close();
            } else {

                Intent target = new Intent(Intent.ACTION_VIEW);
//                target.setDataAndType(Uri.fromFile(pdfFile),"application/pdf");


                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                    target.setDataAndType(FileProvider.getUriForFile(myActivity,
                            BuildConfig.APPLICATION_ID + ".provider",
                            pdfFile), "application/pdf");

                } else {
                    target.setDataAndType(Uri.fromFile(pdfFile), "application/pdf");

                }

                target.setFlags(Intent.FLAG_ACTIVITY_NO_HISTORY);

                Intent intent = Intent.createChooser(target, "Open File");
                try {
                    startActivity(intent);
                } catch (ActivityNotFoundException e) {
                    // Instruct the user to install a PDF reader here, or something
                }
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onResume() {
        super.onResume();
    }

    public class getScheme_Asynctask extends AsyncTask<String, Void, String> {
        Context myActivity;
        ProgressDialog mStepProgressDialog;
        JSONObject jo = new JSONObject();

        public getScheme_Asynctask(Context myActivity) {
            this.myActivity = myActivity;
        }

        @Override
        protected void onPreExecute() {
            super.onPreExecute();
            mStepProgressDialog = new ProgressDialog(myActivity);
            mStepProgressDialog.setMessage("Please wait..");
            mStepProgressDialog.setCancelable(false);
            mStepProgressDialog.show();
        }

        @Override
        protected String doInBackground(String... params) {
            String POST_result = "";
            try {
                File fileLocal = new File("/mnt/sdcard/OTP/" + id + ".pdf");
                DownloadFile(imageUrl, fileLocal);
            } catch (final Exception e) {
                e.printStackTrace();
            }
            return POST_result;
        }

        @Override
        protected void onPostExecute(String result) {
            super.onPostExecute(result);

            pdfFile = new File("/mnt/sdcard/OTP/" + id + ".pdf");

            if (pdfFile.exists()) {
                loadFromLocal();
            } else {

            }
            //
            mStepProgressDialog.dismiss();
        }
    }

    public void loadFromLocal() {
        try {

            ImageView_community_resource_document = (TouchImageView) findViewById(R.id.imgPdfRendered);
            if (currentApiVersion >= Build.VERSION_CODES.LOLLIPOP) {
                ImageView_community_resource_document.setOnTouchListener(new OnSwipeTouchListener(myActivity) {
                    @Override
                    public void onSwipeLeft() {
                        if (maxValuePdfPage > 0) {
                            int currentPage = 1;
                            if (currentPdfPage == 0) {

                                ShowPdfPagesInImageView(currentPdfPage + 1);
                                currentPdfPage = currentPdfPage + 1;
                                currentPage = currentPdfPage + 1;
//                        pdfPageNumberTextView.setText(currentPage + "/" + totalPage);
                                pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#000000'>" + totalPage + "</font>"));
                            } else if (currentPdfPage < maxValuePdfPage) {
                                ShowPdfPagesInImageView(currentPdfPage + 1);
                                currentPdfPage = currentPdfPage + 1;
                                currentPage = currentPdfPage + 1;
//                        pdfPageNumberTextView.setText(currentPage + "/" + totalPage);
                                pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#000000'>" + totalPage + "</font>"));
                            }
                        }
                    }

                    @Override
                    public void onSwipeRight() {
                        if (maxValuePdfPage > 0) {
                            int currentPage = totalPage;
                            if (currentPdfPage == maxValuePdfPage) {
                                currentPage = currentPdfPage;
                                ShowPdfPagesInImageView(currentPdfPage - 1);
                                currentPdfPage = currentPdfPage - 1;
                                pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                            } else if (currentPdfPage > minValuePdfPage) {
                                currentPage = currentPdfPage;
                                ShowPdfPagesInImageView(currentPdfPage - 1);
                                currentPdfPage = currentPdfPage - 1;
//                        pdfPageNumberTextView.setText(currentPage + "/" + totalPage);
                                pdfPageNumberTextView.setText(Html.fromHtml("Pages: " + currentPage + "/<font color='#F58322'>" + totalPage + "</font>"));
                            }
                        }
                        //Toast.makeText(CommunityResourceFileOpen.this, "swipped right", Toast.LENGTH_SHORT).show();

                    }
                });
            }
            readPdfFileAndShowOnImageView();

        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public Boolean DownloadFile(String fileURL, File directory) {
        Boolean isDownloadSuccess;
        try {

            FileOutputStream f = new FileOutputStream(directory);
            URL u = new URL(fileURL);
// URL u = new URL(URLEncoder.encode(fileURL, "utf-8"));
            HttpURLConnection c = (HttpURLConnection) u.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.connect();

            InputStream in = c.getInputStream();

            byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = in.read(buffer)) > 0) {
                f.write(buffer, 0, len1);
            }
            f.close();
            isDownloadSuccess = true;
        } catch (final Exception e) {
            e.printStackTrace();
            isDownloadSuccess = false;
        }
        return isDownloadSuccess;
    }
}
