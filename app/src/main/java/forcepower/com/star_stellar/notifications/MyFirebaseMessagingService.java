package forcepower.com.star_stellar.notifications;


import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_firebase_token;

import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.Build;
import android.os.SystemClock;
import android.text.Html;
import android.text.TextUtils;

import androidx.annotation.RequiresApi;
import androidx.core.app.NotificationCompat;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

import org.json.JSONObject;

import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;
import java.util.Random;

import forcepower.com.star_stellar.Activity.SplashActivity;
import forcepower.com.star_stellar.R;


public class MyFirebaseMessagingService extends FirebaseMessagingService {
    private final String TAG = " BIG_TexT_FLAG_IMMUTABLE ",
            default_notification_channel_id = "1000001010";
    private final int dbResource = R.mipmap.ic_launcher;
    private int notiId = 1001;
    private Context mContext;

    @Override
    public void onMessageReceived(final RemoteMessage remoteMessage) {
        try {
            print_Log_d(TAG, " BIGTExTP_fg_A : " + remoteMessage.getFrom());
            print_Log_d(TAG, " BIGTExTP_fg_B : " + remoteMessage.getData());
            final JSONObject jo1 = new JSONObject(remoteMessage.getData());

            print_Log_d(TAG, " BIGTExTP_fg_B1 : " + jo1.optString("noty_type"));

            print_Log_d(TAG, " BIGTExTP_fg_C : " + remoteMessage.getNotification().getBody());
            print_Log_d(TAG, " BIGTExTP_fg_d : " + remoteMessage.getNotification().getImageUrl());
            print_Log_d(TAG, " BIGTExTP_fg_e : " + remoteMessage.getNotification().getTitle());
            print_Log_d(TAG, " BIGTExTP_fg_f : " + remoteMessage.getSentTime());

            final JSONObject jsonObject = new JSONObject();
            final JSONObject jo = new JSONObject();
            jo.put("title", remoteMessage.getNotification().getTitle());
            jo.put("message", remoteMessage.getNotification().getBody());
            jo.put("image", remoteMessage.getNotification().getImageUrl());
            jo.put("icon", "");
            jo.put("noty_type", jo1.optString("noty_type"));
            jo.put("timestamp", System.currentTimeMillis());
            jsonObject.put("data", jo);
            mContext = this;

            final int min = 1, max = 998;
            notiId = new Random().nextInt((max - min) + 1) + min;

            final Intent resultIntent = new Intent(getApplicationContext(), SplashActivity.class);
            resultIntent.putExtra("noty_type", jo1.optString("noty_type"));
            resultIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
            final PendingIntent resultPendingIntent =
                    PendingIntent.getActivity(
                            mContext,
                            0,
                            resultIntent,
                            PendingIntent.FLAG_UPDATE_CURRENT |
                                    PendingIntent.FLAG_IMMUTABLE
                    );

            final NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(
                    mContext, default_notification_channel_id);

            handleDataMessage(jsonObject, resultPendingIntent, mBuilder);

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void handleDataMessage(final JSONObject json, final PendingIntent resultPendingIntent,
                                   final NotificationCompat.Builder mBuilder) {
        try {
            print_Log_d(TAG + " push_json_data_ ", " " + json.toString());

            final JSONObject data = json.optJSONObject("data");

            final String title = data.optString("title"),

                    noty_type = data.optString("noty_type"),
                    message = data.optString("message"),
                    imageUrl = data.optString("image"),

//          imageUrl =  "https://image.shutterstock.com/image-photo/path-through-forest-trees-nature-600w-1977851039.jpg",
//          message = "In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before the final copy is END",

                    icon = data.optString("icon"),
                    timestamp = data.optString("timestamp");


            //-------------------Added here----------------------------
            if (message.length() > 35) {
                showBigText(title, message, timestamp, resultPendingIntent, mBuilder);
            } else if (TextUtils.isEmpty(imageUrl)) {
                showSmallNotification(mBuilder, title, message, timestamp, resultPendingIntent);
            } else {
                showNotificationMessage(title, message, timestamp, resultPendingIntent, imageUrl, icon, mBuilder);
            }
        } catch (Exception e) {
            print_Log_d(TAG, "Exception: " + e.getMessage());
        }
    }

    public void showNotificationMessage(final String title, final String message, final String timeStamp,
                                        final PendingIntent resultPendingIntent, final String imageUrl,
                                        final String icon, final NotificationCompat.Builder mBuilder) {
        try {


            Bitmap bitmap = null, icon_bitmap = null;
            if (icon != null && icon.length() > 4) {
                icon_bitmap = getBitmapFromURL(icon);
            }
            if (imageUrl != null && imageUrl.length() > 4) {
                bitmap = getBitmapFromURL(imageUrl);
            }

            if (icon_bitmap == null) {
                icon_bitmap = BitmapFactory.decodeResource(mContext.getResources(), dbResource);
            }
            if (bitmap != null) {
                showBigNotification(bitmap, mBuilder, icon_bitmap, title, message, timeStamp, resultPendingIntent);
            } else {
                showSmallNotification(mBuilder, title, message, timeStamp, resultPendingIntent);
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void showSmallNotification(final NotificationCompat.Builder mBuilder,
                                       final String title, final String message, final String timeStamp,
                                       final PendingIntent resultPendingIntent) {
        try {
            final NotificationCompat.InboxStyle inboxStyle = new NotificationCompat.InboxStyle();
            inboxStyle.addLine(Html.fromHtml(message));

            final NotificationManager notificationManager = (NotificationManager) mContext.getSystemService(Context.NOTIFICATION_SERVICE);
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                int importance = NotificationManager.IMPORTANCE_DEFAULT;
                NotificationChannel mChannel = notificationManager.getNotificationChannel(default_notification_channel_id);
                if (mChannel == null) {
                    mChannel = new NotificationChannel(default_notification_channel_id,
                            mContext.getResources().getString(R.string.app_name), importance);
                    mChannel.enableVibration(true);
                    mChannel.setVibrationPattern(new long[]{100, 200, 300, 400, 500, 400, 300, 200, 400});
                    notificationManager.createNotificationChannel(mChannel);
                }

                mBuilder
                        .setAutoCancel(false)
                        .setContentTitle(title)
                        .setContentIntent(resultPendingIntent)
                        .setStyle(inboxStyle)
                        .setWhen(getTimeMilliSec(timeStamp))
                        .setSmallIcon(dbResource)
                        .setContentText(message)
                        .build();
                notificationManager.notify(notiId, mBuilder.build());

            } else {
                final Notification notification = mBuilder
                        .setAutoCancel(false)
                        .setContentTitle(title)
                        .setContentIntent(resultPendingIntent)
                        .setStyle(inboxStyle)
                        .setWhen(getTimeMilliSec(timeStamp))
                        .setSmallIcon(dbResource)
                        .setContentText(message)
                        .build();
                notificationManager.notify(notiId, notification);

            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void showBigNotification(Bitmap bitmap, NotificationCompat.Builder mBuilder,
                                     Bitmap icon_bitmap, String title, String message,
                                     String timeStamp, PendingIntent resultPendingIntent) {
        try {
            final NotificationCompat.BigPictureStyle bigPictureStyle = new NotificationCompat.BigPictureStyle();
            bigPictureStyle.setBigContentTitle(title);
            bigPictureStyle.setSummaryText(Html.fromHtml(message));
            bigPictureStyle.bigPicture(bitmap);
            bigPictureStyle.bigLargeIcon(icon_bitmap);

            final NotificationManager notificationManager = (NotificationManager) mContext.getSystemService(Context.NOTIFICATION_SERVICE);

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                int importance = NotificationManager.IMPORTANCE_DEFAULT;
                NotificationChannel mChannel = notificationManager.getNotificationChannel(default_notification_channel_id);
                if (mChannel == null) {
                    mChannel = new NotificationChannel(default_notification_channel_id, mContext.getResources().getString(R.string.app_name), importance);
                    mChannel.enableVibration(true);
                    mChannel.setVibrationPattern(new long[]{100, 200, 300, 400, 500, 400, 300, 200, 400});
                    notificationManager.createNotificationChannel(mChannel);
                }

                mBuilder
                        .setAutoCancel(false)
                        .setContentTitle(title)
                        .setContentIntent(resultPendingIntent)
                        .setStyle(bigPictureStyle)
                        .setWhen(getTimeMilliSec(timeStamp))
                        .setSmallIcon(dbResource)
                        .build();
                notificationManager.notify(notiId, mBuilder.build());

            } else {
                final Notification notification = mBuilder
                        .setAutoCancel(false)
                        .setContentTitle(title)
                        .setContentIntent(resultPendingIntent)
                        .setStyle(bigPictureStyle)
                        .setWhen(getTimeMilliSec(timeStamp))
                        .setSmallIcon(dbResource)
                        .build();
                notificationManager.notify(notiId, notification);

            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void showBigText(final String title, final String message, final String timeStamp,
                             final PendingIntent resultPendingIntent, final NotificationCompat.Builder mBuilder) {
        try {
            final NotificationCompat.BigTextStyle bigText = new NotificationCompat.BigTextStyle();

            final NotificationManager notificationManager = (NotificationManager) mContext.getSystemService(Context.NOTIFICATION_SERVICE);

            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                int importance = NotificationManager.IMPORTANCE_DEFAULT;
                NotificationChannel mChannel = notificationManager.getNotificationChannel(default_notification_channel_id);
                if (mChannel == null) {
                    mChannel = new NotificationChannel(default_notification_channel_id, mContext.getResources().getString(R.string.app_name), importance);
                    mChannel.enableVibration(true);
                    mChannel.setVibrationPattern(new long[]{100, 200, 300, 400, 500, 400, 300, 200, 400});
                    notificationManager.createNotificationChannel(mChannel);
                }

                mBuilder
                        .setAutoCancel(false)
                        .setContentTitle(title)
                        .setContentIntent(resultPendingIntent)

                        .setContentText(message)
                        .setStyle(bigText)
                        .setWhen(getTimeMilliSec(timeStamp))
                        .setSmallIcon(dbResource)
                        .build();
                notificationManager.notify(notiId, mBuilder.build());

            } else {
                final Notification notification = mBuilder
                        .setAutoCancel(false)
                        .setContentTitle(title)
                        .setContentIntent(resultPendingIntent)
                        .setContentText(message)
                        .setStyle(bigText)
                        .setWhen(getTimeMilliSec(timeStamp))
                        .setSmallIcon(dbResource)
                        .build();
                notificationManager.notify(notiId, notification);

            }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public Bitmap getBitmapFromURL(final String strURL) {
        try {
            final URL url = new URL(strURL);
            final HttpURLConnection connection = (HttpURLConnection) url.openConnection();
            connection.setDoInput(true);
            connection.connect();
            final InputStream input = connection.getInputStream();
            return BitmapFactory.decodeStream(input);
        } catch (IOException e) {
            e.printStackTrace();
            return null;
        }
    }

    public long getTimeMilliSec(final String timeStamp) {
        try {
            return Long.parseLong(timeStamp);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return 0;
    }

    @Override
    public void onNewToken(final String token) {
        try {
            set_firebase_token(this, token);
            print_Log_d(TAG, "Refreshed_token_1 " + token);
            // If you want to send messages to this application instance or
            // manage this apps subscriptions on the server side, send the
            // Instance ID token to your app server.
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}

