# PRD – Moduł Dynamicznej Dostawy PrestaShop

## 1. Przegląd Projektu

### 1.1 Cel Produktu

Stworzenie uniwersalnego modułu dostawy dla PrestaShop 8.x, który dynamicznie oblicza koszt wysyłki na podstawie reguł zdefiniowanych na poziomie produktu. Moduł umożliwia elastyczne zarządzanie kosztami transportu bez konieczności konfigurowania standardowych stref wagowych czy klas wysyłkowych.

### 1.2 Problem Biznesowy

**Główny problem:** Standardowe metody dostawy w PrestaShop nie pozwalają na elastyczne definiowanie kosztów wysyłki per produkt z uwzględnieniem złożonych reguł biznesowych.

- Brak możliwości ustawienia różnych kosztów wysyłki dla różnych produktów w ramach jednego kuriera
- Niemożliwość definiowania warunków darmowej wysyłki (ilościowych i kwotowych) per produkt
- Brak obsługi produktów wymagających osobnej wysyłki (np. dłużyce, palety)
- Skomplikowana konfiguracja przy dużej różnorodności produktów i ich wymagań logistycznych

### 1.3 Wartość Biznesowa

**Główna wartość:** Pełna kontrola nad kosztami wysyłki sterowana z poziomu karty produktu, bez ograniczeń standardowych mechanizmów PrestaShop.

- Eliminacja ręcznych obliczeń kosztów wysyłki dla złożonych zamówień
- Automatyczne stosowanie darmowej wysyłki po spełnieniu warunków ilościowych lub kwotowych
- Obsługa 80-90% realnych scenariuszy logistycznych bez nadmiernej komplikacji
- Możliwość definiowania różnych progów kwotowych dla różnych krajów/stref
- Pełna analityka i raportowanie kosztów wysyłki

**Dodatkowe korzyści:**
- Redukcja błędów w wycenie wysyłki
- Spójność kosztów transportu w całym sklepie
- Łatwa modyfikacja reguł bez ingerencji w kod

---

## 2. Użytkownicy Docelowi

### 2.1 Użytkownik Główny – Administrator IT

**Persona: "Techniczny Opiekun Sklepu"**
- Rola: Administrator odpowiedzialny za konfigurację i utrzymanie PrestaShop
- Kompetencje: zaawansowane techniczne, znajomość architektury PrestaShop
- Potrzeby: szybka konfiguracja modułu, czytelna dokumentacja, stabilność działania
- Oczekiwania: intuicyjny panel konfiguracyjny, logi błędów, możliwość debugowania

### 2.2 Użytkownik Wtórny – Pracownik Działu Produktowego

**Persona: "Manager Katalogu"**
- Rola: Osoba zarządzająca produktami i ich atrybutami
- Kompetencje: średnio-zaawansowane, znajomość backoffice PrestaShop
- Potrzeby: proste pola do uzupełnienia na karcie produktu, jasne etykiety pól
- Oczekiwania: maksymalnie 5-6 pól do uzupełnienia, tooltips z wyjaśnieniami

### 2.3 Użytkownik Trzeci – Klient Sklepu

**Persona: "Kupujący"**
- Rola: Osoba składająca zamówienie w sklepie
- Kompetencje: podstawowe, użytkownik e-commerce
- Potrzeby: jasna informacja o koszcie wysyłki przed złożeniem zamówienia
- Oczekiwania: jedna, czytelna kwota wysyłki w koszyku (bez skomplikowanego rozbicia)

---

## 3. Wymagania Funkcjonalne

### 3.1 Konfiguracja Modułu (Panel Administracyjny)

#### 3.1.1 Ustawienia Globalne

**User Stories:**
- Jako administrator chcę zdefiniować globalny próg kwotowy dla darmowej wysyłki, aby produkty mogły się do niego odwoływać
- Jako administrator chcę ustawić różne progi kwotowe dla różnych stref dostawy
- Jako administrator chcę wybrać metodę liczenia progu (cały koszyk vs tylko objęte produkty)
- Jako administrator chcę nadać nazwę kurierowi widoczną dla klienta

**Funkcjonalności:**
- Pole: Nazwa kuriera (domyślnie: "Wysyłka kurierem")
- Tabela: Progi kwotowe per strefa/kraj
- Przełącznik: Metoda liczenia progu kwotowego (cały koszyk / tylko produkty objęte regułą)
- Włączenie/wyłączenie modułu
- Przypisanie do wybranych stref dostawy

---

#### 3.1.2 Konfiguracja Pól Produktowych

**User Stories:**
- Jako administrator chcę, aby na karcie produktu pojawiły się dodatkowe pola transportowe
- Jako administrator chcę, aby pola były dostępne również dla kombinacji produktu

**Funkcjonalności:**
- Dodanie zakładki/sekcji "Transport" na karcie produktu
- Pola dostępne na poziomie produktu głównego
- Możliwość nadpisania ustawień dla poszczególnych kombinacji

---

### 3.2 Pola Produktowe (Karta Produktu)

#### 3.2.1 Darmowa Wysyłka Bezwarunkowa

**User Stories:**
- Jako manager katalogu chcę oznaczyć produkt jako "zawsze darmowa wysyłka"
- Jako manager katalogu chcę, aby ta opcja wyłączała wszystkie inne pola transportowe

**Funkcjonalności:**
- Checkbox: "Produkt ma darmową wysyłkę"
- Priorytet najwyższy – gdy zaznaczone, pozostałe pola nieaktywne
- Koszt wysyłki dla tego produktu: 0 zł, niezależnie od ilości

---

#### 3.2.2 Bazowy Koszt Transportu

**User Stories:**
- Jako manager katalogu chcę wpisać koszt wysyłki dla produktu
- Jako manager katalogu chcę, aby koszt był walidowany (liczba >= 0)

**Funkcjonalności:**
- Pole numeryczne: "Bazowy koszt transportu" (PLN)
- Walidacja: liczba >= 0
- Wartość domyślna: 0

---

#### 3.2.3 Mnożenie Kosztu przez Ilość

**User Stories:**
- Jako manager katalogu chcę określić, czy koszt wysyłki ma być mnożony przez ilość sztuk
- Jako manager katalogu chcę mieć wybór: koszt jednorazowy vs koszt per sztuka

**Funkcjonalności:**
- Checkbox: "Mnóż koszt przez ilość sztuk"
- Gdy niezaznaczone: koszt liczony raz, niezależnie od ilości
- Gdy zaznaczone: koszt = bazowy × ilość sztuk

---

#### 3.2.4 Warunek Darmowej Wysyłki – Ilościowy

**User Stories:**
- Jako manager katalogu chcę ustawić próg ilościowy dla darmowej wysyłki
- Jako manager katalogu chcę, aby po osiągnięciu progu wysyłka była darmowa

**Funkcjonalności:**
- Pole numeryczne: "Darmowa wysyłka od X sztuk"
- Wartość 0 lub puste = brak progu ilościowego
- Walidacja: liczba całkowita >= 0

---

#### 3.2.5 Warunek Darmowej Wysyłki – Kwotowy

**User Stories:**
- Jako manager katalogu chcę oznaczyć, że produkt podlega globalnemu progowi kwotowemu
- Jako manager katalogu chcę, aby system automatycznie sprawdzał próg dla strefy klienta

**Funkcjonalności:**
- Checkbox: "Produkt podlega progowi kwotowemu"
- Gdy zaznaczone: system sprawdza globalny próg dla strefy dostawy
- Próg kwotowy liczony wg ustawień globalnych (cały koszyk lub tylko objęte produkty)

---

#### 3.2.6 Osobna Paczka

**User Stories:**
- Jako manager katalogu chcę oznaczyć produkt jako "zawsze osobna paczka"
- Jako manager katalogu chcę, aby koszt tego produktu był liczony niezależnie od innych

**Funkcjonalności:**
- Checkbox: "Produkt zawsze wysyłany osobną paczką"
- Gdy zaznaczone: koszt wysyłki produktu liczony osobno
- Brak możliwości łączenia z innymi produktami w jedną przesyłkę

---

### 3.3 Obliczanie Kosztu Wysyłki (Frontend)

#### 3.3.1 Algorytm Obliczania

**User Stories:**
- Jako klient chcę widzieć poprawnie obliczony koszt wysyłki w koszyku
- Jako klient chcę, aby darmowa wysyłka była automatycznie naliczana po spełnieniu warunków

**Funkcjonalności:**
- Grupowanie produktów: osobna paczka vs wspólna wysyłka
- Dla każdej grupy:
  1. Sprawdzenie darmowej wysyłki bezwarunkowej
  2. Sprawdzenie progu ilościowego
  3. Sprawdzenie progu kwotowego (dla strefy klienta)
  4. Obliczenie kosztu bazowego (z/bez mnożenia przez ilość)
- Sumowanie kosztów wszystkich grup
- Wyświetlenie jednej sumarycznej kwoty

---

#### 3.3.2 Wyświetlanie w Koszyku

**User Stories:**
- Jako klient chcę widzieć jasną informację o koszcie wysyłki
- Jako klient nie chcę być przytłoczony szczegółami rozbicia

**Funkcjonalności:**
- Wyświetlanie: "Wysyłka kurierem: X zł"
- Dla darmowej wysyłki: "Wysyłka kurierem: 0 zł" lub "Darmowa wysyłka"
- Brak szczegółowego rozbicia (suma zbiorcza)

---

### 3.4 Analityka i Raportowanie

#### 3.4.1 Logi Obliczeń

**User Stories:**
- Jako administrator chcę mieć dostęp do logów obliczeń kosztów wysyłki
- Jako administrator chcę móc debugować problemy z nieprawidłowymi wycenami

**Funkcjonalności:**
- Logowanie każdego obliczenia kosztu wysyłki
- Dane w logu: produkty, ilości, zastosowane reguły, wynik końcowy
- Możliwość włączenia/wyłączenia logowania
- Retencja logów: konfigurowalna (domyślnie 30 dni)

---

#### 3.4.2 Raporty Statystyczne

**User Stories:**
- Jako administrator chcę widzieć statystyki kosztów wysyłki
- Jako administrator chcę analizować, które produkty generują największe koszty transportu

**Funkcjonalności:**
- Dashboard z podstawowymi statystykami
- Raport: średni koszt wysyłki per zamówienie
- Raport: produkty z najwyższymi kosztami transportu
- Raport: zamówienia z darmową wysyłką (ilościową vs kwotową)
- Eksport raportów do CSV

---

## 4. Wymagania Niefunkcjonalne

### 4.1 Wydajność
- Obliczenie kosztu wysyłki: < 100ms dla koszyka do 50 produktów
- Brak wpływu na czas ładowania strony produktu

### 4.2 Kompatybilność
- PrestaShop 8.x (8.0, 8.1, 8.2)
- PHP 8.1+
- Kompatybilność z popularnymi szablonami

### 4.3 Bezpieczeństwo
- Walidacja wszystkich danych wejściowych
- Ochrona przed SQL injection
- Zgodność z CSRF protection PrestaShop

### 4.4 Lokalizacja
- Interfejs backoffice: język polski
- Możliwość tłumaczenia na inne języki (standard PrestaShop)

---

## 5. Scenariusze Testowe

### 5.1 Scenariusz: Talerze
- 1-2 sztuki: koszt 60 zł (paczka kurierska)
- 3-30 sztuk: koszt 140 zł (paleta) – realizowane przez próg ilościowy
- Powyżej progu kwotowego: darmowa wysyłka

### 5.2 Scenariusz: Grabie
- Koszt bazowy: 40 zł
- Darmowa wysyłka od 3 sztuk
- Zawsze osobna paczka

### 5.3 Scenariusz: Mix (Grabie + Łopatki)
- Grabie: osobna wysyłka 40 zł
- Łopatki: wspólna wysyłka 25 zł
- Suma: 65 zł

### 5.4 Scenariusz: Darmowa wysyłka kwotowa
- Produkty z zaznaczonym progiem kwotowym
- Wartość koszyka przekracza próg dla strefy PL
- Wysyłka: 0 zł

---

## 6. Ograniczenia i Założenia

### 6.1 Ograniczenia
- Moduł nie zarządza realnymi przewoźnikami (integracje API kurierów)
- Moduł nie optymalizuje pakowania (bin packing)
- Brak obsługi multistore (jedna instalacja = jeden sklep)

### 6.2 Założenia
- Moduł współistnieje z innymi metodami dostawy w PrestaShop
- Administrator odpowiada za spójność danych produktowych
- Koszty w PLN (waluta domyślna), przeliczanie wg standardu PrestaShop

---

## 7. Harmonogram (Fazy)

### Faza 1: MVP
- Podstawowe pola produktowe (darmowa wysyłka, koszt bazowy, mnożenie)
- Obliczanie kosztu w koszyku
- Panel konfiguracji globalnej

### Faza 2: Rozszerzenie
- Progi ilościowe i kwotowe
- Osobna paczka
- Obsługa kombinacji produktów

### Faza 3: Analityka
- Logowanie obliczeń
- Dashboard statystyk
- Eksport raportów

---

## 8. Metryki Sukcesu

- 100% poprawność obliczeń kosztów wysyłki w testowanych scenariuszach
- Czas wdrożenia modułu przez administratora: < 1 godzina
- Czas konfiguracji produktu: < 2 minuty per produkt
- Brak błędów krytycznych w pierwszym miesiącu po wdrożeniu
