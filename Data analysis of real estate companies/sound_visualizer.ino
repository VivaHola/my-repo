#include <Adafruit_NeoPixel.h>

// Define the pin where the data line is connected
#define LED_PIN 3

// Define the number of LEDs in the strip
#define NUM_LEDS 72

// Create a NeoPixel object
Adafruit_NeoPixel strip = Adafruit_NeoPixel(NUM_LEDS, LED_PIN, NEO_GRB + NEO_KHZ800);

// Configuration parameters
#define BRIGHTNESS 255 // Min: 0, Max: 255
#define SATURATION 255 // Min: 0, Max: 255
#define HUE_INIT 0 // < 255
#define HUE_CHANGE 10 // Larger value for more noticeable color change

// Define the analog pin for the loudness sensor
#define LOUDNESS_PIN A0

// Initial threshold for loudness to activate LEDs
#define SOUND_THRESHOLD 90 // Adjust this value based on your environment

// Parameters for the weighted moving average filter
#define NUM_SAMPLES 10
#define BASELINE_NOISE 50 // Adjust this based on your environment noise level

byte dynamicHue = HUE_INIT;
int val = BRIGHTNESS;
int sensorValues[NUM_SAMPLES];
int sensorIndex = 0;
float sensorTotal = 0;
float sensorAverage = 0;

void setup() {
  // Initialize the NeoPixel strip
  strip.begin();
  strip.setBrightness(BRIGHTNESS);
  strip.show(); // Initialize all pixels to 'off'

  // Initialize serial communication at 9600 bits per second
  Serial.begin(9600);

  // Initialize sensor values for weighted moving average filter
  for (int i = 0; i < NUM_SAMPLES; i++) {
    sensorValues[i] = 0;
  }
}

void loop() {
  // Read the loudness sensor value
  int sensorValue = analogRead(LOUDNESS_PIN);

  // Update the weighted moving average filter
  sensorTotal -= sensorValues[sensorIndex];
  sensorValues[sensorIndex] = sensorValue;
  sensorTotal += sensorValue;
  sensorIndex = (sensorIndex + 1) % NUM_SAMPLES;

  sensorAverage = sensorTotal / NUM_SAMPLES;

  // Print out the sensor average value to the Serial Monitor (for debugging)
  Serial.print("Sensor Average Value: ");
  Serial.println(sensorAverage);

  if (sensorAverage < SOUND_THRESHOLD + BASELINE_NOISE) {
    // Turn off all LEDs if the sound is below the threshold + baseline noise
    for (int i = 0; i < NUM_LEDS; i++) {
      strip.setPixelColor(i, 0, 0, 0); // Turn off LED
    }
  } else {
    // Map the sensor average value to a range suitable for brightness
    val = map(sensorAverage, SOUND_THRESHOLD + BASELINE_NOISE, 1023, 50, BRIGHTNESS); // Start from 50 for more noticeable changes

    // Shift all the LEDs outward from the center
    for (int i = NUM_LEDS / 2; i > 0; i--) {
      strip.setPixelColor(NUM_LEDS / 2 - i, strip.getPixelColor(NUM_LEDS / 2 - i + 1));
      strip.setPixelColor(NUM_LEDS / 2 + i - 1, strip.getPixelColor(NUM_LEDS / 2 + i - 2));
    }

    // Set the center LEDs to the new color based on loudness
    strip.setPixelColor(NUM_LEDS / 2, strip.ColorHSV(dynamicHue * 256, SATURATION, val));
    strip.setPixelColor(NUM_LEDS / 2 - 1, strip.ColorHSV(dynamicHue * 256, SATURATION, val));

    // Increment the hue
    dynamicHue += HUE_CHANGE;
    if (dynamicHue >= 255) {
      dynamicHue = 0;
    }
  }

  // Update the LED strip
  strip.show();

  // Adjust delay to control the speed of the flowing effect
  delay(50); // Increase delay for more noticeable transitions
}

