/**
 * forked from BasicHTTPClient.ino
 *
 * sending analog data to HTTP server
 * blinking LED on successfull WiFi connection
 */

#include <WiFi.h>
#include <WiFiMulti.h>
#include <HTTPClient.h>

#define USE_SERIAL Serial

WiFiMulti wifiMulti;

void setup() {

    // setup serial for 
    USE_SERIAL.begin(115200);
  	
    // setup blinking LED
    pinMode(2, OUTPUT);
    digitalWrite(2, LOW);
    
    // setup WiFi AP
    wifiMulti.addAP("albin", "albinalbin");

}

void loop() {

    // wait for WiFi connection
    digitalWrite(2, HIGH);  // turn the LED on (HIGH is the voltage level)
     
    if((wifiMulti.run() == WL_CONNECTED)) {

        HTTPClient http;

        USE_SERIAL.print("[HTTP] begin...\n");
        
        String url = "https://albin.si/analog/?data=" + String(analogRead(34));
        http.begin(url.c_str());

        USE_SERIAL.print("[HTTP] GET...\n");
        USE_SERIAL.print(url + "\n" );
        // start connection and send HTTP header
        int httpCode = http.GET();

        // httpCode will be negative on error
        if(httpCode > 0) {
            // HTTP header has been send and Server response header has been handled
            USE_SERIAL.printf("[HTTP] GET... code: %d\n", httpCode);

            // file found at server
            if(httpCode == HTTP_CODE_OK) {
                String payload = http.getString();
                USE_SERIAL.println(payload);
            }
        } else {
            USE_SERIAL.printf("[HTTP] GET... failed, error: %s\n", http.errorToString(httpCode).c_str());
        }

        http.end();
    }
    digitalWrite(2, LOW);   // turn the LED off by making the voltage LOW
    delay(1000);  
}
