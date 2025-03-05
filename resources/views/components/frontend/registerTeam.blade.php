<div class="container">
    <h1>Meldung für {{ $event->ueberschrift }}</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('notification'))
        <div class="alert alert-warning">
            {{ session('notification') }}
        </div>
    @endif
    <form action="{{ route('RegattaTeam.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="regatta_id" id="regatta_id" class="form-control" value="{{$event->id}}">
         <div class="form-group">
            <label for="teamname">Teamname:</label>
            <input type="text" name="teamname" id="teamname" class="form-control {{ $errors->has('teamname') ? 'alert-warning' : '' }}" value="{{ old('teamname') }}">
            @if($errors->has('teamname'))
                <div class="alert alert-danger small" role="alert">{{ $errors->first('teamname') }}</div>
            @endif
        </div>
        <div class="form-group">
           <label for="teamcaptain">Teamcaptain / Teamkapitänin:</label>
           <input type="text" name="teamcaptain" id="teamcaptain" class="form-control {{ $errors->has('teamcaptain') ? 'alert-warning' : '' }}" value="{{ old('teamcaptain') }}">
           @if($errors->has('teamcaptain'))
               <div class="alert alert-danger small" role="alert">{{ $errors->first('teamcaptain') }}</div>
           @endif
        </div>
        <div class="form-group">
           <label for="verein">Verein / Firma / Institution:</label>
           <input type="text" name="verein" id="verein" class="form-control {{ $errors->has('verein') ? 'alert-warning' : '' }}" value="{{ old('verein') }}">
           @if($errors->has('verein'))
               <div class="alert alert-danger small" role="alert">{{ $errors->first('verein') }}</div>
           @endif
        </div>
        <div class="form-group">
           <label for="strasse">Strasse:</label>
           <input type="text" name="strasse" id="strasse" class="form-control {{ $errors->has('strasse') ? 'alert-warning' : '' }}" value="{{ old('strasse') }}">
           @if($errors->has('strasse'))
               <div class="alert alert-danger small" role="alert">{{ $errors->first('strasse') }}</div>
           @endif
        </div>
        <div class="form-group">
           <label for="plz">PLZ:</label>
           <input type="text" name="plz" id="plz" class="form-control {{ $errors->has('plz') ? 'alert-warning' : '' }}" value="{{ old('plz') }}">
           @if($errors->has('plz'))
               <div class="alert alert-danger small" role="alert">{{ $errors->first('plz') }}</div>
           @endif
        </div>
        <div class="form-group">
           <label for="ort">Ort:</label>
           <input type="text" name="ort" id="ort" class="form-control {{ $errors->has('ort') ? 'alert-warning' : '' }}" value="{{ old('ort') }}">
           @if($errors->has('ort'))
               <div class="alert alert-danger small" role="alert">{{ $errors->first('ort') }}</div>
           @endif
        </div>
        <div class="form-group">
           <label for="telefon">Telefon:</label>
           <input type="text" name="telefon" id="telefon" class="form-control {{ $errors->has('telefon') ? 'alert-warning' : '' }}" value="{{ old('telefon') }}">
           @if($errors->has('telefon'))
               <div class="alert alert-danger small" role="alert">{{ $errors->first('telefon') }}</div>
           @endif
        </div>
        <div class="form-group">
           <label for="email">Email:</label>
           <input type="email" name="email" id="email" class="form-control {{ $errors->has('email') ? 'alert-warning' : '' }}" value="{{ old('email') }}">
           @if($errors->has('email'))
               <div class="alert alert-danger small" role="alert">{{ $errors->first('email') }}</div>
           @endif
        </div>
        <div class="form-group">
           <label for="homepage">Homepage:</label>
           <input type="text" name="homepage" id="homepage" class="form-control {{ $errors->has('homepage') ? 'alert-warning' : '' }}" value="{{ old('homepage') }}"
                  placeholder="http://... oder https://..." title="Bitte gebe eine gültige URL ein, die mit http:// oder https:// beginnt.">
           @if($errors->has('homepage'))
               <div class="alert alert-danger small" role="alert">{{ $errors->first('homepage') }}</div>
           @endif
        </div>
        <div class="form-group">
           <label for="gruppe_id">Wertung / Klasse:</label>
           <select name="gruppe_id" id="gruppe_id" class="form-control" required>
               <option value="0">Wertung / Klasse wählen</option>
               @foreach($raceTypes as $raceType)
                   <option value="{{ $raceType->id }}" {{ old('gruppe_id') == $raceType->id ? 'selected' : '' }}>{{ $raceType->typ }}</option>
               @endforeach
           </select>
           @if($errors->has('gruppe_id'))
               <div class="alert alert-danger small" role="alert">{{ $errors->first('gruppe_id') }}</div>
           @endif
        </div>
        <div class="form-group">
            <label for="Teamfoto">Teamfoto:</label>
            <input type="file" name="Teamfoto" id="Teamfoto" class="form-control" value="{{ old('Teamfoto') }}">
            @if($errors->has('Teamfoto'))
                <div class="alert alert-danger small" role="alert">{{ $errors->first('Teamfoto') }}</div>
            @endif
        </div>
        <div class="form-group">
            <label for="beschreibung">
                Kurze Info zum Team:<br>
                Seit wann aktiv?<br>
                Wer sind wir?<br>
                Besonderheiten usw.<br>
                Der Text erscheint auf der Homepage und dient als Sprecherinformation:
            </label>
            <textarea name="beschreibung" id="beschreibung" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="kommentar">Information an den Veranstalter:</label>
            <textarea name="kommentar" id="kommentar" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="werbung">Wie haben Sie von uns erfahren:</label>
            <select name="werbung" id="werbung" class="form-control" size="1" width="10">
                <option value="0" selected>nicht ausgewählt</option>
                <option value="1">kel-datteln.de Homepage</option>
                <option value="2">Day of Dragons Homepage</option>
                <option value="3">Kanucup-Datteln Homepage</option>
                <option value="4">Plakatwerbung</option>
                <option value="5">Flyer</option>
                <option value="6">Empfehlung von Sportfreunden</option>
                <option value="7">Radio</option>
                <option value="8">Drachenboot-Liga</option>
                <option value="9">Einladungsmail</option>
                <option value="10">Presse</option>
                <option value="12">dragonboat.online</option>
                <option value="13">lokalkompass.de</option>
                <option value="11">Sonstiges</option>
            </select>
        </div>

        @if($event->einverstaendnis != '')
            <div class="form-group">
                <label for="einverstaendnis">Teilnahmebedingungen / Einverständniserklärung:</label>
                <p>
                    {!! $event->einverstaendnis !!}
                </p>
                <p class="form-check">
                    <input type="checkbox" name="einverstaendnis" id="einverstaendnis" class="form-check-input" {{ old('einverstaendnis') ? 'checked' : '' }}>
                    <label class="form-check-label" for="einverstaendnis">
                       Ich habe die Teilnahmebedingungen / Einverständniserklärung gelesen und erkläre mich damit einverstanden. Dies geschieht mit einem Haken im Kontrollfeld.
                    </label>
                </p>
                @if($errors->has('einverstaendnis'))
                    <div class="alert alert-danger small" role="alert">{{ $errors->first('einverstaendnis') }}</div>
                @endif
            </div>
        @endif

        <div class="form-group">
            <label for="mailen">Möchten sie über weiter Events Informiert werden:</label>
            <div class="form-check">
                <input type="checkbox" name="mailen" id="mailen" class="form-check-input" {{ old('mailen') ? 'checked' : '' }}>
                <label class="form-check-label" for="mailen">
                    Ja, ich möchte informiert werden.
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="captcha">Was ist {{ $num1 }} + {{ $num2 }}?</label>
            <input type="text" name="captcha" id="captcha" class="form-control" required>
            <input type="hidden" name="num1" value="{{ $num1 }}">
            <input type="hidden" name="num2" value="{{ $num2 }}">
        </div>

        <button type="submit" class="btn btn-primary">Melden</button>
        <br><br>
    </form>
</div>
