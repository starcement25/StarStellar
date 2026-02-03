package org.forcepower.starcement.custom

import kotlinx.coroutines.DelicateCoroutinesApi
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.async
import kotlinx.coroutines.launch

/*
Ref.
https://stackoverflow.com/questions/58767733/the-asynctask-api-is-deprecated-in-android-11-what-are-the-alternatives
 */
abstract class AsyncTaskCoroutine<I, O> {
    var result: O? = null

    //private var result: O
    open fun onPreExecute() {}

    open fun onPostExecute(result: O?) {}
    abstract fun doInBackground(vararg params: I): O

    @OptIn(DelicateCoroutinesApi::class)
    fun <T> execute(vararg input: I) {
        GlobalScope.launch(Dispatchers.Main) {
            onPreExecute()
            callAsync(*input)
        }
    }

    @OptIn(DelicateCoroutinesApi::class)
    private suspend fun callAsync(vararg input: I) {
        GlobalScope.async(Dispatchers.IO) {
            result = doInBackground(*input)
        }.await()
        GlobalScope.launch(Dispatchers.Main) {

            onPostExecute(result)


        }
    }
}