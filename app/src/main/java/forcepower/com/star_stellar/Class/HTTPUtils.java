package forcepower.com.star_stellar.Class;


import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;

import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;

import java.util.ArrayList;
import java.util.concurrent.TimeUnit;

import cz.msebera.android.httpclient.NameValuePair;
import okhttp3.FormBody;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

public final class HTTPUtils {

    public static boolean isConnectionPossible(final Context mContext) {
        final ConnectivityManager cm = (ConnectivityManager) mContext.getSystemService(Context.CONNECTIVITY_SERVICE);
        final NetworkInfo netInfo = cm.getActiveNetworkInfo();
        return (netInfo != null && netInfo.isConnected());
    }

    public static String getDataByHTTP_GET(final Context context, final String url) {

        final OkHttpClient client = new OkHttpClient();
        String responseFromServer = "Network Failure";
        try {
            if (isConnectionPossible(context)) {
                final Request request = new Request.Builder()
                        .url(url)
                        .build();
                final Response response = client.newCall(request).execute();
                responseFromServer = response.body().string();
            }
        } catch (Exception e) {
            e.printStackTrace();
            responseFromServer = "Network Failure";
        }

        print_Log_d("amitabha2715_getDataByHTTP_GET ", url + "");
        print_Log_d("RES ", responseFromServer + "");
        return responseFromServer;
    }


    public static String getDataByHTTP_POST(final Context context, final String url,
                                            final ArrayList<NameValuePair> nameValuePairs) {
        final OkHttpClient client = new OkHttpClient();
//		final OkHttpClient client = new OkHttpClient.Builder()
//				.connectTimeout(15, TimeUnit.SECONDS) // 15 seconds
//				.readTimeout(15, TimeUnit.SECONDS)    // 15 seconds
//				.writeTimeout(15, TimeUnit.SECONDS)   // 15 seconds
//				.build();
        String responseFromServer = "Network Failure";
        if (isConnectionPossible(context)) {
            try {
                final FormBody.Builder formBuilder = new FormBody.Builder();
                if (nameValuePairs != null) {
                    for (final NameValuePair innerList : nameValuePairs) {
                        final String key = innerList.getName();
                        final String value = innerList.getValue();

                        formBuilder.add(key, value);
                    }
                }
                final RequestBody formBody = formBuilder.build();
                final Request request = new Request.Builder()
                        .url(url)
                        .post(formBody)
                        .build();
                final Response response = client.newCall(request).execute();
                responseFromServer = response.body().string();
            } catch (Exception e) {
                e.printStackTrace();
                print_Log_d("error94_ ", e.toString());
                responseFromServer = "Network Failure";
            }
        }
        print_Log_d("StAR_getDataByHTTP_POST ", url + "");
        print_Log_d("STaR_RES ", responseFromServer + "");
        return responseFromServer;
    }

    public static String getDataByHTTP_POST_Time(final Context context, final String url,
                                                 final ArrayList<NameValuePair> nameValuePairs) {
//		final OkHttpClient client = new OkHttpClient();
        final OkHttpClient client = new OkHttpClient.Builder()
                .connectTimeout(35, TimeUnit.SECONDS) // 15 seconds
                .readTimeout(35, TimeUnit.SECONDS)    // 15 seconds
                .writeTimeout(35, TimeUnit.SECONDS)   // 15 seconds
                .build();
        String responseFromServer = "Network Failure";
        if (isConnectionPossible(context)) {
            try {
                final FormBody.Builder formBuilder = new FormBody.Builder();
                if (nameValuePairs != null) {
                    for (final NameValuePair innerList : nameValuePairs) {
                        final String key = innerList.getName();
                        final String value = innerList.getValue();

                        formBuilder.add(key, value);
                    }
                }
                final RequestBody formBody = formBuilder.build();
                final Request request = new Request.Builder()
                        .url(url)
                        .post(formBody)
                        .build();
                final Response response = client.newCall(request).execute();
                responseFromServer = response.body().string();
            } catch (Exception e) {
                e.printStackTrace();
                print_Log_d("error94_ ", e.toString());
                responseFromServer = "Network Failure";
            }
        }
        print_Log_d("StAR_getDataByHTTP_POST ", url + "");
        print_Log_d("STaR_RES ", responseFromServer + "");

        return responseFromServer;
    }
}
