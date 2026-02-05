# RULES – Zasady pracy nad modułem customcarrier

## Podstawowe zasady

0. **Zawsze odpowiadaj w języku polskim**

1. **Kolejność działań:** Najpierw analizuj istniejący kod PrestaShop i hooki, a dopiero później generuj kod modułu.

2. **Transparentność:** Jeśli czegoś nie umiesz lub nie znasz API PrestaShop 8.x, powiedz wprost. Nie brnij w kłamstwa ani niepewne rozwiązania.

3. **Kod i komentarze w języku angielskim:** Wszystkie nazwy zmiennych, funkcji, klas oraz komentarze w kodzie pisz po angielsku.

4. **Bez hard coding:** NIGDY nie stosuj wartości na sztywno w kodzie. Używaj konfiguracji, stałych lub parametrów.

---

## Współpraca i komunikacja

5. **Human-in-the-loop:** Stosuj podejście współpracy z człowiekiem. Razem robimy pair programming – pytaj o potwierdzenie PRZED KAŻDYMI zmianami.

6. **Komunikacja:** Przy każdym kroku mów krótko CO robisz. Nie tłumacz działania hooków ani architektury – tylko konkretne działania.

7. **Limit prób:** Jeśli nie możesz rozwiązać problemu po 2 próbach, poproś człowieka o pomoc lub dodatkowy kontekst.

8. **Problemy z dokumentacją:** Jeśli masz problem z API PrestaShop lub brakuje dokumentacji, poproś o dołączenie odpowiedniej dokumentacji lub przykładów z innych modułów.

---

## Jakość kodu

9. **Wysoka jakość:** Stosuj jedynie porządne i eleganckie rozwiązania. Unikaj hacków i obejść.

10. **Zgodność z PrestaShop:** Używaj natywnych mechanizmów PrestaShop:
    - Hooki zamiast nadpisywania plików core
    - Doctrine dla operacji bazodanowych
    - Symfony Forms dla formularzy backoffice
    - Configuration dla ustawień modułu

11. **Problemy z typami:** Jeśli nie wiesz jak rozwiązać problem z typami w PHP, zastosuj komentarz `/** @var Type */` lub poproś o pomoc.

12. **Kompletność zadań:** NIGDY nie zostawiaj komentarzy typu TODO podczas wykonywania zadania. Każde zadanie musi być wykonane od początku do końca w całości i poprawnie.

13. **Walidacja danych:** Zawsze waliduj dane wejściowe. Używaj Symfony Validator lub natywnej walidacji PrestaShop.

14. **Obsługa błędów:** Implementuj obsługę wyjątków. Loguj błędy do plików. Nie pokazuj szczegółów błędów użytkownikowi końcowemu.

---

## Planowanie i analiza

15. **Analiza istniejącego kodu:** Gdy podajesz plan wdrożenia nowej funkcji, zawsze przeanalizuj aktualny kod modułu i dostosuj do niego swój plan.

16. **Sprawdzanie hooków:** Przed implementacją funkcji sprawdź jakie hooki są dostępne w PrestaShop 8.x dla danego przypadku użycia.

17. **Plan przed kodem:** Najpierw opracuj plan – napisz co, jak i dlaczego chcesz zrobić. Poczekaj na akceptację przed pisaniem kodu.

---

## Praca z PrestaShop

18. **Struktura modułu:** Zachowuj standardową strukturę katalogów modułu PrestaShop. Nie twórz niestandardowych lokalizacji plików.

19. **Namespace:** Używaj namespace `CustomCarrier\` dla wszystkich klas w katalogu `src/`.

20. **Autoloading:** Korzystaj z composer autoload (PSR-4) dla klas w `src/`.

21. **Tłumaczenia:** Wszystkie teksty widoczne dla użytkownika umieszczaj w systemie tłumaczeń PrestaShop (`$this->trans()`).

22. **Migracje bazy:** Przy zmianach struktury bazy danych twórz skrypty upgrade w katalogu `upgrade/`.

---

## Praca z środowiskiem lokalnym (MAMP/XAMPP)

23. **Ścieżki:** Używaj relatywnych ścieżek wewnątrz modułu. Nie hardcoduj ścieżek absolutnych.

24. **Testowanie lokalne:** Pracujesz TYLKO na lokalnej instalacji. NIE wykonujesz commitów ani operacji git – to robi człowiek ręcznie.

25. **Logi deweloperskie:** W trybie deweloperskim włącz pełne logowanie. Sprawdzaj logi w `var/logs/` po każdej operacji.

26. **Cache:** Po zmianach w serwisach lub konfiguracji czyść cache PrestaShop (`rm -rf var/cache/*`).

---

## Baza danych

27. **NIE DOTYKAJ istniejącej bazy PrestaShop:** Twórz TYLKO nowe tabele dla modułu. NIGDY nie modyfikuj tabel core PrestaShop.

28. **Prefiks tabel:** Zawsze używaj `PREFIX_` lub `_DB_PREFIX_` dla nazw tabel. Nigdy nie hardcoduj `ps_`.

29. **Prepared statements:** Używaj Doctrine lub `pSQL()` / `bqSQL()` dla zapytań. Nigdy nie wstawiaj danych bezpośrednio do SQL.

30. **Migracje:** Nie modyfikuj struktury tabel ręcznie. Twórz skrypty SQL i upgrade.

---

## Testowanie

31. **Scenariusze testowe:** Przed oznaczeniem funkcji jako gotowej, przetestuj ją na scenariuszach z PRD (talerze, grabie, mix).

32. **Edge cases:** Testuj przypadki brzegowe:
    - Pusty koszyk
    - Produkt bez ustawień transportowych
    - Kombinacje bez nadpisań
    - Strefa bez progu kwotowego

33. **Regresja:** Po każdej zmianie sprawdź czy nie zepsułeś istniejącej funkcjonalności.

---

## Git i wersjonowanie

34. **NIE wykonujesz operacji git:** Nie robisz commitów, nie robisz merge. Wszystkie operacje git wykonuje człowiek ręcznie.

35. **Nie commituj (informacja dla człowieka):**
    - `var/cache/`
    - `var/logs/`
    - `.DS_Store`
    - Plików konfiguracyjnych z hasłami

---

## Komunikacja z użytkownikiem

36. **Informacje o błędach:** Dla użytkownika końcowego pokazuj przyjazne komunikaty. Techniczne szczegóły tylko w logach.

37. **Walidacja formularzy:** Pokazuj jasne komunikaty walidacji przy polach formularza.

38. **Potwierdzenia:** Po zapisie ustawień pokazuj komunikat sukcesu.

---

## Priorytet implementacji

Kolejność implementacji funkcji:

1. **MVP:** Podstawowa struktura modułu, instalacja, pola produktowe, obliczanie kosztu
2. **Progi:** Progi ilościowe i kwotowe, osobna paczka
3. **Kombinacje:** Obsługa kombinacji produktów
4. **Import/Export:** CSV i XML
5. **Analityka:** Logowanie, raporty, dashboard

---

## Checklisty

### Przed każdą zmianą:
- [ ] Masz akceptację człowieka na wprowadzenie zmiany
- [ ] Plan jest zatwierdzony

### Po wprowadzeniu zmian:
- [ ] Kod działa lokalnie
- [ ] Brak błędów PHP
- [ ] Cache wyczyszczony i przetestowany
- [ ] Komentarze po angielsku
- [ ] Brak TODO w kodzie
- [ ] Brak wartości hardcoded
