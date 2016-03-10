<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  
	xmlns:set="http://exslt.org/sets">

<xsl:output method="html" encoding="utf-8" indent="yes" doctype-system="about:legacy-compat" />

<xsl:template match="students">
  <xsl:param name="bid"><xsl:value-of select="component/value_db" /></xsl:param>
  <xsl:variable name="students" select="student" />

  <div class="head">
	  <label>Tracking:</label>
	  <xsl:value-of select="subject/value" />
	  <xsl:text> </xsl:text>
	  <xsl:value-of select="component/value" />

	  <ul>
		<xsl:call-template name="asslabel">
		  <xsl:with-param name="assindex" select="1"/>
		  <xsl:with-param name="year" select="2013" />
		</xsl:call-template>
		<xsl:call-template name="asslabel">
		  <xsl:with-param name="assindex" select="1"/>
		  <xsl:with-param name="year" select="2012" />
		</xsl:call-template>
	  </ul>
  </div>

  <div id="chart" class="center"></div>

  <div class="hidden">
	<table id="graph">
	  <tfoot id="graph_axis">
		<tr>
		  <xsl:for-each select="$students">
			  <xsl:element name="td">
				<xsl:attribute name="id"><xsl:value-of select="id_db/text()" /></xsl:attribute>
				<xsl:value-of select="forename/value/text()" />
			  </xsl:element>
		  </xsl:for-each>
		</tr>
	  </tfoot>
	  <tbody id="graph_data1">
		<xsl:call-template name="assrow">
		  <xsl:with-param name="assindex" select="1"/>
		  <xsl:with-param name="bid" select="$bid" />
		  <xsl:with-param name="year" select="2013" />
		  <xsl:with-param name="students" select="$students" />
		</xsl:call-template>
	  </tbody>
	  <tbody id="graph_data2">
		<xsl:call-template name="assrow">
		  <xsl:with-param name="assindex" select="1"/>
		  <xsl:with-param name="bid" select="$bid" />
		  <xsl:with-param name="year" select="2012" />
		  <xsl:with-param name="students" select="$students" />
		</xsl:call-template>
	  </tbody>
	</table>

  </div>
  <div class='spacer'></div>

<hr />

</xsl:template>


<xsl:template name="assrow">
  <xsl:param name="assindex">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="year"></xsl:param>
  <xsl:param name="students"></xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(asstable/ass[year=$year and contains(label,'SS')]/id_db)+1"/>
  </xsl:variable>
  <xsl:variable name="asses" select="asstable/ass[year=$year and contains(label,'SS')]/id_db" />
  <xsl:if test="$assindex &lt; $maxindex">
	<tr>
	  <th scope="row">
		<xsl:value-of select="$asses[$assindex]" />
		<xsl:value-of select="$bid" />
	  </th>

	  <xsl:for-each select="$students">
		<td>
		  <xsl:choose>
			<xsl:when test="count(assessments/assessment[id_db=$asses[$assindex]]) &gt; 0">
			  <xsl:value-of select="assessments/assessment[id_db=$asses[$assindex]]/value" />
			</xsl:when>
			<xsl:otherwise>
			  <xsl:text>0</xsl:text>
			</xsl:otherwise>
		  </xsl:choose>
		</td>
	  </xsl:for-each>
	</tr>

	<xsl:call-template name="assrow">
	  <xsl:with-param name="assindex" select="$assindex + 1"/>
	  <xsl:with-param name="bid" select="$bid" />
	  <xsl:with-param name="year" select="$year" />
	  <xsl:with-param name="students" select="$students" />
	</xsl:call-template>


  </xsl:if>
</xsl:template>


<xsl:template name="asslabel">
  <xsl:param name="assindex">1</xsl:param>
  <xsl:param name="year"></xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(asstable/ass[year=$year and contains(label,'SS')]/id_db)+1"/>
  </xsl:variable>
  <xsl:variable name="asses" select="asstable/ass[year=$year and contains(label,'SS')]" />
  <xsl:if test="$assindex &lt; $maxindex">

	<li>
	  <xsl:value-of select="$asses[$assindex]/label" />
	  <label><xsl:value-of select="$asses[$assindex]/date" /></label>
	</li>

	<xsl:call-template name="assrow">
	  <xsl:with-param name="assindex" select="$assindex + 1"/>
	  <xsl:with-param name="year" select="$year" />
	</xsl:call-template>

  </xsl:if>
</xsl:template>





</xsl:stylesheet>
