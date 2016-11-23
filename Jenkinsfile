import groovy.json.JsonSlurperClassic 


@NonCPS
def jsonParse(def json) {
    new groovy.json.JsonSlurperClassic().parseText(json)
}

node('setekhmaseter') {
   stage('install tools') {

   git 'https://ebarboni@github.com/ebarboni/pourlahorde.git'

   sh "composer install"
}
stage('get data'){

sh 'sleep 0.5'
sh 'mkdir -p app/_data/'

sh "curl -o app/_data/guild.json 'https://eu.api.battle.net/wow/guild/Elune/Pour%20la%20horde?fields=achievements%2Cchallenge%2Cmembers%2Cnews&locale=fr_FR&apikey=${env.APIKEY}'"
def f = env.WORKSPACE + '/app/_data/guild.json'
echo "${f}"
def character = jsonParse(readFile(f));
for (charact  in character.get('members') ) {
echo charact.get('character').get('name')
 def n =  charact.get('character').get('name').toString()
sh "curl -o app/_data/${n}.json 'https://eu.api.battle.net/wow/character/Elune/${n}?fields=professions&locale=fr_FR&apikey=${env.APIKEY}'"
sh 'sleep 1'
}
}
stage ('build') {
   sh './vendor/bin/sculpin generate --env=prod'
}

}